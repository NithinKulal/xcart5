<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\AmazonS3Images\Logic\Import\Step;

/**
 * Move resized images to Amazon S3
 */
class MoveResizedImages extends \XLite\Logic\Import\Step\AStep
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
        return static::t('Resized images uploaded');
    }

    /**
     * Get note
     *
     * @return string
     */
    public function getNote()
    {
        return static::t('Uploading resized image files to Amazon S3...');
    }

    /**
     * Get step weight
     * Move images to Amazon S3 should be the last step of import process
     *
     * @return integer
     */
    public function getWeight()
    {
        return 9010;
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
            if (\XLite\Core\Database::getRepo($class)->hasNoS3Images(true, true)) {
                // Found resized images not moved to S3, get first image item
                $chunk = \XLite\Core\Database::getRepo($class)->findNoS3ResizedImages(false, 1, true);
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
        if (!isset($this->getOptions()->commonData['s3rCount'])) {
            $count = 0;

            if (\XLite\Module\CDev\AmazonS3Images\Core\S3::getInstance()->isValid()) {
                foreach (\XLite\Model\Repo\Base\Image::getManagedRepositories() as $class) {
                    $count += \XLite\Core\Database::getRepo($class)->findNoS3ResizedImages(true, null, true);
                }
            }
            $this->getOptions()->commonData['s3rCount'] = $count;
        }

        return $this->getOptions()->commonData['s3rCount'];
    }

    /**
     * Process item
     *
     * @param mixed $item Item
     *
     * @return boolean
     */
    protected function processItem($itemData)
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
                
                if (empty($this->getOptions()->commonData['s3rProcessed'])) {
                    $this->getOptions()->commonData['s3rProcessed'] = 0;
                }

                $this->getOptions()->commonData['s3rProcessed']++;

                \XLite\Core\Database::getEM()->flush();
            }
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
            'Resized image files uploaded: X out of Y with errors',
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
            'Resized image files uploaded: X out of Y',
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

        if (!empty($this->getOptions()->commonData['s3rProcessed'])) {
            $list[] = array(
                'text' => static::t('Resized images moved to Amazon S3: {{count}}', array('count' => $this->getOptions()->commonData['s3rProcessed'])),
            );
        }

        return $list;
    }

    /**
     * Finalize step
     *
     * @return void
     */
    public function finalize()
    {
        parent::finalize();

        // Reset needMigration flag for all images
        foreach (\XLite\Model\Repo\Base\Image::getManagedRepositories() as $entityClass) {
            \XLite\Core\Database::getRepo($entityClass)->updateNeedMigration(false);
        }
    }
}
