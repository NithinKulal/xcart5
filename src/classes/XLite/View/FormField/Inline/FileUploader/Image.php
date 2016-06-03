<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Inline\FileUploader;

/**
 * Image
 */
class Image extends \XLite\View\FormField\Inline\Base\Single
{
    /**
     * Save value
     *
     * @return void
     */
    public function saveValue()
    {
        $errors = array();

        foreach ($this->getFields() as $field) {
            $error = $this->getEntity()->processFiles(
                $field['field'][static::FIELD_NAME],
                $field['widget']->getValue() ?: array()
            );

            if ($error) {
                $errors = array_merge($errors, $error);
            }
        }

        if ($errors) {
            $this->processErrors($errors);
        }
    }

    /**
     * Process file upload errors.
     * $errors has format: array( array(<message>,<message params>), ... )
     *
     * @param array $errors Array of errors
     *
     * @return void
     */
    protected function processErrors($errors)
    {
        foreach ($errors as $error) {
            \XLite\Core\TopMessage::addError(static::t($error[0], !empty($error[1]) ? $error[1] : array()));
        }
    }

    /**
     * Get container class
     *
     * @return string
     */
    protected function getContainerClass()
    {
        return parent::getContainerClass() . ' inline-file-uploader';
    }

    /**
     * Get initial field parameters
     *
     * @param array $field Field data
     *
     * @return array
     */
    protected function getFieldParams(array $field)
    {
        $list = parent::getFieldParams($field);

        $list[\XLite\View\FormField\FileUploader\AFileUploader::PARAM_MAX_WIDTH] = 58;
        $list[\XLite\View\FormField\FileUploader\AFileUploader::PARAM_MAX_HEIGHT] = 58;

        return $list;
    }

    /**
     * Define form field
     *
     * @return string
     */
    protected function defineFieldClass()
    {
        return 'XLite\View\FormField\FileUploader\Image';
    }

    /**
     * Check - field is editable or not
     *
     * @return boolean
     */
    protected function hasSeparateView()
    {
        return false;
    }
}
