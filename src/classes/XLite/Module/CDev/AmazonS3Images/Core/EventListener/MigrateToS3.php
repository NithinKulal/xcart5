<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\AmazonS3Images\Core\EventListener;

/**
 * Migrate to Amazon S3
 */
class MigrateToS3 extends \XLite\Core\EventListener\Base\Countable
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
        return 'migrateToS3';
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
        if (empty($itemData['size'])) {
            $this->processImageItem($itemData['item']);

        } else {
            $this->processResizedImageItem($itemData);
        }

        // Return true as we should process all found images and ignore errors
        return true;
    }

    /**
     * Process image item
     *
     * @param \XLite\Model\Base\Image $item Item model
     *
     * @return boolean
     */
    protected function processImageItem($item)
    {
        $result = false;

        if (\XLite\Model\Base\Image::STORAGE_S3 != $item->getStorageType()) {

            // Move entity image to S3 location

            if (\Includes\Utils\FileManager::isFileReadable($item->getStoragePath())) {
                $path = tempnam(LC_DIR_TMP, 'migrate_file');

                file_put_contents($path, $item->getBody());

                if (\Includes\Utils\FileManager::isExists($path)) {
                    $localPath = $item->isURL() ? null : $item->getStoragePath();
                    $allowRemove = $item->isAllowRemoveFile();
                    $result = $item->loadFromLocalFile($path, $item->getFileName() ?: basename($item->getPath()));
                    if ($result && $item->getStorageType() === \XLite\Model\Base\Image::STORAGE_S3) {
                        $this->processedImages[] = $item->getPath();
                        if ($allowRemove && $localPath && \Includes\Utils\FileManager::isExists($localPath)) {
                            \Includes\Utils\FileManager::deleteFile($localPath);
                        }
                        \XLite\Core\Database::getEM()->flush($item);
                    }
                    \Includes\Utils\FileManager::deleteFile($path);
                }

            } else {
                // Search for same item already moved to S3
                if (in_array($item->getPath(), $this->processedImages)) {
                    $sameItem = true;

                } else {
                    $sameItem = $item->getRepository()->findSameByPath($item);
                }

                if ($sameItem) {
                    // Do not move image to S3, just update its storage type in database
                    $item->setStorageType(\XLite\Model\Base\Image::STORAGE_S3);
                    $result = true;
                }
            }

            if (!$result) {
                if (!isset($this->record['s3_error_count'])) {
                    $this->record['s3_error_count'] = 0;
                }
                $this->record['s3_error_count']++;
                $this->recordFailedItem($item);
                \XLite\Logger::getInstance()->log(
                    'Couldn\'t move image ' . $item->getStoragePath() . ' (local file system to Amazon S3)',
                    LOG_ERR
                );
            }
        }

        return $result;
    }

    /**
     * @param \XLite\Model\Base\Image $item
     */
    protected function recordFailedItem($item)
    {
        $repo = ltrim(get_class($item), '\\');

        if (!isset($this->record['failed_items'][$repo])) {
            $this->record['failed_items'][$repo] = [];
        }

        $this->record['failed_items'][$repo][] = $item->getId();
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
        $size = $itemData['size'];

        $name = basename($item->getPath());

        $localPath = $item->getLocalResizedPath($size, $name);

        if (\Includes\Utils\FileManager::isExists($localPath)) {

            $path = $item->getResizedPath($size, $name);

            $basename = $item->getFileName() ?: basename($item->getPath());
            $headers = array();
            $headers['Content-Type'] = $item->getMime();
            $headers['Content-Disposition'] = 'inline; filename="' . $basename . '"';

            if ($item->getS3()->copy($localPath, $path, $headers)) {
                $icons = $item->getS3icons();
                $icons[$path] = true;
                $item->setS3icons($icons);
                \XLite\Core\Database::getEM()->flush();
                \Includes\Utils\FileManager::deleteFile($localPath);
                $result = true;

            } else {

                if (!isset($this->record['s3_error_count'])) {
                    $this->record['s3_error_count'] = 0;
                }

                $this->record['s3_error_count']++;
                \XLite\Logger::getInstance()->log(
                    'Couldn\'t move resized image ' . $localPath . ' (local file system to Amazon S3)',
                    LOG_ERR
                );
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
            $count += \XLite\Core\Database::getRepo($class)->countAllNoS3Images();
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
            $excluded = isset($this->record['failed_items'][$class])
                ? $this->record['failed_items'][$class]
                : [];
            if (\XLite\Core\Database::getRepo($class)->hasNoS3Images(null, null, $excluded)) {
                // Search for entities with images stored in non-S3 locations
                $images = \XLite\Core\Database::getRepo($class)->findNoS3Images($length, null, $excluded);
                foreach ($images as $image) {
                    $chunk[] = array(
                        'item' => $image,
                    );
                }
                $length -= count($chunk);

                if (0 >= $length) {
                    $stop = true;
                }
            }

            if (!$stop) {
                // Search for entities with resized icons stored on non-S3 locations
                $chunk = array_merge($chunk, \XLite\Core\Database::getRepo($class)->findNoS3ResizedImages(false, $length));
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
