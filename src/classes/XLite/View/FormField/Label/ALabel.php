<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Label;

/**
 * ALabel
 */
abstract class ALabel extends \XLite\View\FormField\AFormField
{

    /**
     * Widget param names
     */
    const PARAM_UNESCAPE = 'unescape';

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
     * Define widget params
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            static::PARAM_UNESCAPE => new \XLite\Model\WidgetParam\TypeBool('Un-escape value', false),
        );
    }

    /**
     * Get label value
     *
     * @return string
     */
    protected function getLabelValue()
    {
        $value = strval($this->getValue());

        if (!$this->getParam(static::PARAM_UNESCAPE)) {
            $value = func_htmlspecialchars($value);
        }

        return $value;
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
