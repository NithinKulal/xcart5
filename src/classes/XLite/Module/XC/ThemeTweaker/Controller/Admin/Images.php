<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\Controller\Admin;

/**
 * Custom CSS images controller
 */
class Images extends \XLite\Controller\Admin\Images implements \XLite\Base\IDecorator
{
    /**
     * Update action 
     *
     * @return void
     */
    protected function doActionUpdate()
    {
        parent::doActionUpdate();

        $this->updateCustomImages();
    }

    /**
     * Update custom images
     *
     * @return void
     */
    protected function updateCustomImages()
    {
        $dir = \XLite\Module\XC\ThemeTweaker\Main::getThemeDir() . 'images' . LC_DS;

        if (
            $_FILES
            && $_FILES['new_images']
            && $_FILES['new_images']['name']
        ) {
            if (!\Includes\Utils\FileManager::isExists($dir)) {
                \Includes\Utils\FileManager::mkdirRecursive($dir);
            }

            if (\Includes\Utils\FileManager::isDirWriteable($dir)) {
                foreach ($_FILES['new_images']['name'] as $i => $data) {
                    $tmpPath = $_FILES['new_images']['tmp_name'][$i];

                    if (!$tmpPath) {
                        continue;
                    }

                    if (\Includes\Utils\FileManager::isImage($tmpPath)
                        && \Includes\Utils\FileManager::isImageExtension($data)
                    ) {
                        \Includes\Utils\FileManager::moveUploadedFileByMultiple('new_images', $i, $dir);
                    } else {
                        \XLite\Core\TopMessage::addError(
                            'The "{{file}}" file was not uploaded',
                            array('file' => $data)
                        );
                    }
                }

            } else {
                \XLite\Core\TopMessage::addError(
                    'The directory {{dir}} does not exist or is not writable.',
                    array(
                        'dir' => $dir
                    )
                );
            }
        }

        $delete = \XLite\Core\Request::getInstance()->delete;

        if (
            $delete
            && is_array($delete)
        ) {
            foreach ($delete as $key => $file) {
                if ($file) {
                    \Includes\Utils\FileManager::deleteFile($dir . $file);
                }
            }
        }
    }
}
