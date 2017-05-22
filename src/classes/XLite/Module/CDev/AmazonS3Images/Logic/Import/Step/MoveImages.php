<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\AmazonS3Images\Logic\Import\Step;

/**
 * Move images to Amazon S3
 */
class MoveImages extends \XLite\Logic\Import\Step\AStep
{
    /**
     * Static cache of processed images paths
     *
     * @var array
     */
    protected $processedImages = array();

    /**
     * Get final note
     *
     * @return string
     */
    public function getFinalNote()
    {
        return static::t('Images uploaded');
    }

    /**
     * Get note
     *
     * @return string
     */
    public function getNote()
    {
        return static::t('Uploading image files to Amazon S3...');
    }

    /**
     * Get step weight
     * Move images to Amazon S3 should be the last step of import process
     *
     * @return integer
     */
    public function getWeight()
    {
        return 9000;
    }

    /**
     * Process row
     *
     * @return boolean
     */
    public function process()
    {
        $chunk = array();
        $result = false;
        foreach (\XLite\Model\Repo\Base\Image::getManagedRepositories() as $class) {
            if (\XLite\Core\Database::getRepo($class)->hasNoS3Images(false, true)) {
                $chunk = \XLite\Core\Database::getRepo($class)->findNoS3Images(1, true);
                break;
            }
        }

        if ($chunk) {
            $result = $this->processItem(reset($chunk));
        }

        return $result;
    }

    /**
     * \Counable::count
     *
     * @return integer
     */
    public function count()
    {
        if (!isset($this->getOptions()->commonData['s3Count'])) {
            $count = 0;

            if (\XLite\Module\CDev\AmazonS3Images\Core\S3::getInstance()->isValid()) {
                foreach (\XLite\Model\Repo\Base\Image::getManagedRepositories() as $class) {
                    $count += \XLite\Core\Database::getRepo($class)->countNoS3Images(true);
                }
            }
            $this->getOptions()->commonData['s3Count'] = $count;
        }

        return $this->getOptions()->commonData['s3Count'];
    }

    /**
     * Process item
     *
     * @param \XLite\Model\Base\Image $item Item
     *
     * @return boolean
     */
    protected function processItem($item)
    {
        $result = false;

        if (\XLite\Model\Base\Image::STORAGE_S3 != $item->getStorageType()) {
            if (\Includes\Utils\FileManager::isFileReadable($item->getStoragePath())) {

                $path = tempnam(LC_DIR_TMP, 'migrate_file');
                file_put_contents($path, $item->getBody());

                if (\Includes\Utils\FileManager::isExists($path)) {

                    $localPath = $item->isURL() ? null : $item->getStoragePath();
                    $allowRemove = $item->isAllowRemoveFile();
                    $result = $item->loadFromLocalFile($path, $item->getFileName() ?: basename($item->getPath()));
                    if ($result) {
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

            if ($result) {
                if (empty($this->getOptions()->commonData['s3Processed'])) {
                    $this->getOptions()->commonData['s3Processed'] = 0;
                }

                $this->getOptions()->commonData['s3Processed']++;

                \XLite\Core\Database::getEM()->flush();
            }

        } else {
            $result = true;
        }

        return $result;
    }

    /**
     * Check - allowed step or not
     *
     * @return boolean
     */
    public function isAllowed()
    {
        return parent::isAllowed()
            && $this->count() > 0;
    }

    /**
     * Get error language label
     *
     * @return array
     */
    public function getErrorLanguageLabel()
    {
        $options = $this->getOptions();

        return static::t(
            'Image files uploaded: X out of Y with errors',
            array(
                'X'      => $options->position,
                'Y'      => $this->count(),
                'errors' => $options->errorsCount,
                'warns'  => $options->warningsCount,
            )
        );
    }

    /**
     * Get normal language label
     *
     * @return array
     */
    public function getNormalLanguageLabel()
    {
        $options = $this->getOptions();

        return static::t(
            'Image files uploaded: X out of Y',
            array(
                'X' => $options->position,
                'Y' => $this->count(),
            )
        );
    }

    /**
     * Get messages
     *
     * @return array
     */
    public function getMessages()
    {
        $list = parent::getMessages();

        if (!empty($this->getOptions()->commonData['s3Processed'])) {
            $list[] = array(
                'text' => static::t('Images moved to Amazon S3: {{count}}', array('count' => $this->getOptions()->commonData['s3Processed'])),
            );
        }

        return $list;
    }
}
