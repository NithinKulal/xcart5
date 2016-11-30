<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Label;


class TranslationLabel extends \XLite\View\FormField\AFormField
{
    /**
     * Return field type
     *
     * @return string
     */
    public function getFieldType()
    {
        return self::FIELD_TYPE_LABEL;
    }

    /**
     * Get label value
     *
     * @return string
     */
    protected function getLabelValue()
    {
        $value = static::t($this->getValue());

        return $value != $this->getValue() ? $value : '';
    }

    /**
     * Return field template
     *
     * @return string
     */
    protected function getFieldTemplate()
    {
        return 'label/translation_label.twig';
    }

    /**
     * Return url for to edit label
     *
     * @return string
     */
    protected function getLabelEditURL()
    {
        return $this->buildURL('labels', '', ['substring' => $this->getValue()]);
    }

    /**
     * Set the form field as "form control" (some major styling will be applied)
     *
     * @return boolean
     */
    protected function isFormControl()
    {
        return false;
    }
}