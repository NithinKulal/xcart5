<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\AmazonS3Images\Core\EventListener;

/**
 * Migrate from Amazon S3
 */
class MigrateFromS3 extends \XLite\Core\EventListener\Base\Countable
{
    const CHUNK_LENGTH = 10;

    /**
     * Static cache of processed images paths
     *
     * @var array
     */
    protected $processedImages = array();

    /**
     * Get event name
     *
     * @return string
     */
    protected function getEventName()
    {
        return 'migrateFromS3';
    }

    /**
     * Process item
     *
     * @param array $itemData Item data
     *
     * @return boolean
     */
    protected function processItem($itemData)
    {
        if (empty($itemData['path'])) {
            $this->processImageItem($itemData['item']);

        } else {
            $this->processResizedImageItem($itemData);
        }

        return true;
    }

    /**
     * Process image item
     *
     * @param \XLite\Model\Base\Image $item Image model
     *
     * @return boolean
     */
    protected function processImageItem($item)
    {
        $result = false;

        if ($item->isFileExists()) {

            $path = tempnam(LC_DIR_TMP, 'migrate_file');
            file_put_contents($path, $item->getBody());

            if (file_exists($path)) {
                $item->setS3Forbid(true);
                $localPath = $item->getStoragePath();
                $result = $item->loadFromLocalFile($path, $item->getFileName() ?: basename($item->getPath()));
                if ($result) {
                    $this->processedImages[] = $item->getPath();
                    if ($localPath) {
                        \XLite\Module\CDev\AmazonS3Images\Core\S3::getInstance()->delete($localPath);
                    }
                }
                unlink($path);
            }

        } else {
            // Search for same item in local file system
            if (in_array($item->getPath(), $this->processedImages)) {
                $sameItem = true;

            } else {
                $sameItem = $item->getRepository()->findSameByPath($item, false);
            }

            if ($sameItem) {
                $item->setStorageType(\XLite\Model\Base\Image::STORAGE_RELATIVE);
                $result = true;
            }
        }

        if (!$result) {
            if (!isset($this->record['s3_error_count'])) {
                $this->record['s3_error_count'] = 0;
            }
            $this->record['s3_error_count']++;
            \XLite\Logger::getInstance()->log(
                'Couldn\'t move image ' . $item->getPath() . ' (Amazon S3 to local file system)',
                LOG_ERR
            );
        }

        return $result;
    }

    /**
     * Process resized image item
     *
     * @param array $itemData Image item data
     *
     * @return boolean
     */
    protected function processResizedImageItem($itemData)
    {
        $result = false;

        $item = $itemData['item'];
        $s3Path = $itemData['path'];
        $size = $item->detectSizeByPath($s3Path);

        if ($size) {

            list($width, $height) = $size;
            $size = ($width ?: 'x') . '.' . ($height ?: 'x');

            $item->setS3Forbid(true);
            $s3icons = $item->getS3icons();

            // Make ob_start() to prevent operation break caused by potential exception from AWS API
            ob_start();
            $body = \XLite\Module\CDev\AmazonS3Images\Core\S3::getInstance()->read($s3Path);
            ob_end_clean();

            if (!empty($body)) {

                $name = pathinfo($item->getPath(), \PATHINFO_FILENAME) . '.' . $item->getExtension();
                $localPath = $item->getLocalResizedPath($size, $name);

                $result = \Includes\Utils\FileManager::write($localPath, $body);

                if ($result && \Includes\Utils\FileManager::isExists($localPath)) {
                    \XLite\Module\CDev\AmazonS3Images\Core\S3::getInstance()->delete($s3Path);
                    unset($s3icons[$s3Path]);
                    $item->setS3icons($s3icons);
                }

                if (!$result) {
                    if (!isset($this->record['s3_error_count'])) {
                        $this->record['s3_error_count'] = 0;
                    }
                    $this->record['s3_error_count']++;
                    \XLite\Logger::getInstance()->log(
                        'Couldn\'t move image ' . $s3Path . ' (Amazon S3 to local file system)',
                        LOG_ERR
                    );
                }

            } else {
                unset($s3icons[$s3Path]);
                $item->setS3icons($s3icons);
            }
        }

        return $result;
    }

    /**
     * Check step valid state
     *
     * @return boolean
     */
    protected function isStepValid()
    {
        return parent::isStepValid()
            && \XLite\Module\CDev\AmazonS3Images\Core\S3::getInstance()->isValid();
    }

    /**
     * Get images list length 
     * 
     * @return integer
     */
    protected function getLength()
    {
        $count = 0;

        foreach (\XLite\Model\Repo\Base\Image::getManagedRepositories() as $class) {
            $count += \XLite\Core\Database::getRepo($class)->countAllS3Images();
        }

        return $count;
    }

    /**
     * Get items
     *
     * @return array
     */
    protected function getItems()
    {
        $length = static::CHUNK_LENGTH;
        $chunk = array();
        $stop = false;

        foreach (\XLite\Model\Repo\Base\Image::getManagedRepositories() as $class) {

            if (\XLite\Core\Database::getRepo($class)->hasS3Images()) {

                // Search for entities with images stored in S3 location
                $images = \XLite\Core\Database::getRepo($class)->findS3Images(static::CHUNK_LENGTH);

                foreach ($images as $image) {
                    $chunk[] = array(
                        'item' => $image
                    );
                }

                $length -= count($chunk);

                if (0 >= $length) {
                    $stop = true;
                }
            }

            if (!$stop) {
                // Search for resized icons stored in S3 location
                $chunk = array_merge($chunk, \XLite\Core\Database::getRepo($class)->findS3ResizedImages(false, $length));
                $length -= count($chunk);
                $stop = ($length <= 0);
            }

            if ($stop) {
                break;
            }
        }

        return $chunk;
    }

    /**
     * Finish task
     *
     * @return void
     */
    protected function finishTask()
    {
        parent::finishTask();

        if (isset($this->record['s3_error_count']) && 0 < $this->record['s3_error_count']) {
            $this->errors[] = static::t('Couldn\'t move X images. See log for details.', array('count' => $this->record['s3_error_count']));
        }
    }
}
