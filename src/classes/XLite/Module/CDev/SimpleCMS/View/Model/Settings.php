<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\SimpleCMS\View\Model;

/**
 * Settings dialog model widget
 */
abstract class Settings extends \XLite\View\Model\Settings implements \XLite\Base\IDecorator
{
    /**
     * Logo & Favicon fields
     *
     * @var array
     */
    static protected $logoFaviconFields = array('logo', 'favicon', 'appleIcon');

    /**
     * Logo & Favicon validation flag
     *
     * @var boolean
     */
    protected $logoFaviconValidation = true;

    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/CDev/SimpleCMS/settings.css';

        return $list;
    }

    /**
     * Defines the subdirectory where images (logo, favicon) will be stored
     *
     * @return string
     */
    protected static function getLogoFaviconSubDir()
    {
        return \Includes\Utils\FileManager::getRelativePath(LC_DIR_IMAGES, LC_DIR) . LC_DS . 'simplecms' . LC_DS;
    }

    /**
     * Defines the server directory where images (logo, favicon) will be stored
     *
     * @return string
     */
    protected static function getLogoFaviconDir()
    {
        return LC_DIR . LC_DS . static::getLogoFaviconSubDir();
    }

    /**
     * Check for the form errors
     *
     * @return boolean
     */
    public function isValid()
    {
        return parent::isValid() && $this->logoFaviconValidation;
    }

    /**
     * Populate model object properties by the passed data
     *
     * @param array $data Data to set
     *
     * @return void
     */
    protected function setModelProperties(array $data)
    {
        $options = $this->getEditableOptions();

        if ('logo_favicon' === $this->getTarget()) {
            foreach ($options as $k => $v) {
                if (in_array($v->name, static::$logoFaviconFields, true)) {
                    $data[$v->name] = $this->prepareImageData($v->value, $v->name);
                }
            }
        }

        parent::setModelProperties($data);
    }

    /**
     * Get schema fields
     *
     * @return array
     */
    public function getSchemaFieldsForSection($section)
    {
        $list = parent::getSchemaFieldsForSection($section);

        if ('logo_favicon' === $this->getTarget()
            || ('module' === $this->getTarget()
                && $this->getModule()
                && 'CDev\SimpleCMS' === $this->getModule()->getActualName()
            )
        ) {
            foreach ($list as $k => $v) {
                $id = is_object($v) && property_exists($v, 'name') ? $v->name : $k;
                if (('logo_favicon' === $this->getTarget()
                        && !in_array($id, static::$logoFaviconFields, true)
                    )
                    || ('logo_favicon' !== $this->getTarget()
                        && in_array($id, static::$logoFaviconFields, true)
                    )
                ) {
                    unset($list[$k]);
                }
            }
        }

        return $list;
    }

    /**
     * Additional preparations for images.
     * Upload them into specific directory
     *
     * @param string $optionValue Option value
     * @param string $imageType   Image type
     *
     * @return string
     */
    protected function prepareImageData($optionValue, $imageType)
    {
        $dir = static::getLogoFaviconDir();
        if (
            $_FILES
            && $_FILES[$imageType]
            && $_FILES[$imageType]['name']
        ) {
            $path = null;

            $realName = preg_replace('/([^a-zA-Z0-9_\-\.]+)/', '_', $_FILES[$imageType]['name']);
            $realName = $imageType . '_' . $realName;

            $validImage = $imageType === 'appleIcon'
                ? $this->isValidAppleIcon($_FILES[$imageType]['tmp_name'], $realName)
                : $this->isImage($_FILES[$imageType]['tmp_name'], $realName);

            if ($validImage) {

                if (!\Includes\Utils\FileManager::isDir($dir)) {
                    \Includes\Utils\FileManager::mkdirRecursive($dir);
                }

                if (\Includes\Utils\FileManager::isDir($dir)) {

                    // Remove file with same name as uploaded file in the destination directory
                    \Includes\Utils\FileManager::deleteFile(
                        $dir . LC_DS . ('favicon' === $imageType ? static::FAVICON : $realName)
                    );

                    // Move uploaded file to destination directory
                    $path = \Includes\Utils\FileManager::moveUploadedFile(
                        $imageType,
                        $dir,
                        'favicon' === $imageType ? static::FAVICON : $realName
                    );

                    if ($path) {
                        if ($optionValue && 'logo' === $imageType && basename($optionValue) != $realName) {
                            // Remove old image file
                            \Includes\Utils\FileManager::deleteFile($dir . basename($optionValue));
                        }
                        $optionValue = static::getLogoFaviconSubDir() . basename($path);
                    }
                }

                if (!isset($path)) {
                    $this->logoFaviconValidation = false;
                    \XLite\Core\TopMessage::addError(
                        'The "{{file}}" file was not uploaded',
                        array('file' => $realName)
                    );
                }

            } else {
                $this->logoFaviconValidation = false;

                if ($imageType === 'appleIcon') {
                    \XLite\Core\TopMessage::addError(
                        'The AppleIcon image could not be uploaded (Unallowed image type. Must be a .png image with the resolution of 192x192 px)',
                        array(
                            'file' => $realName,
                        )
                    );
                } else {
                    \XLite\Core\TopMessage::addError(
                        'The "{{file}}" file is not allowed image and was not uploaded. Allowed images are: {{extensions}}',
                        array(
                            'file' => $realName,
                            'extensions' => implode(', ', $this->getImageExtensions()),
                        )
                    );
                }
            }

        } elseif (\XLite\Core\Request::getInstance()->useDefaultImage[$imageType]) {
            if ($optionValue) {
                \Includes\Utils\FileManager::deleteFile($dir . basename($optionValue));
            }
            $optionValue = '';
        }

        return $optionValue;
    }

    /**
     * Check if file is valid image
     *
     * @param string $path Temporary uploaded file path
     * @param string $name Real file name
     *
     * @return boolean
     */
    protected function isImage($path, $name)
    {
        return $this->hasImageName($name)
            && \Includes\Utils\FileManager::isImageExtension($name)
            && \Includes\Utils\FileManager::isImage($path);
    }

    /**
     * Check if file is valid image
     *
     * @param string $path Temporary uploaded file path
     * @param string $name Real file name
     *
     * @return boolean
     */
    protected function isValidAppleIcon($path, $name)
    {
        return $this->hasImageName($name)
            && strtolower(pathinfo($name, PATHINFO_EXTENSION)) === 'png'
            && \Includes\Utils\FileManager::isImage($path)
            && $this->isValidResolution($path, '192x192');
    }

    /**
     * @param $path
     * @param $resolution
     *
     * @return bool
     */
    protected function isValidResolution($path, $resolution)
    {
        $data = @getimagesize($path);

        return is_array($data)
            ? $data[0] . 'x' . $data[1] === $resolution
            : true;
    }
    /**
     * Return true if file has non-empty name
     *
     * @param string $path File path
     *
     * @return boolean
     */
    protected function hasImageName($path)
    {
        return 0 < strlen(trim(pathinfo($path, PATHINFO_FILENAME)));
    }

    /**
     * Get list of allowed image extensions
     *
     * @return array
     */
    protected function getImageExtensions()
    {
        return array('gif', 'jpg', 'jpeg', 'png', 'ico');
    }
}
