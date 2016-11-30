<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Input;

/**
 * Common checkbox
 */
class Checkbox extends \XLite\View\FormField\Input\AInput
{
    /**
     * Widget param names
     */
    const PARAM_IS_CHECKED = 'isChecked';
    const PARAM_CAPTION    = 'caption';

    /**
     * Return field type
     *
     * @return string
     */
    public function getFieldType()
    {
        return self::FIELD_TYPE_CHECKBOX;
    }

    /**
     * Define widget params
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            self::PARAM_IS_CHECKED => new \XLite\Model\WidgetParam\TypeBool('Is checked', false),
            self::PARAM_CAPTION    => new \XLite\Model\WidgetParam\TypeString('Caption', '', false),
        ];
    }

    /**
     * Determines if checkbox is checked
     *
     * @return boolean
     */
    protected function isChecked()
    {
        return $this->getParam(self::PARAM_IS_CHECKED) || $this->checkSavedValue();
    }

    /**
     * checkSavedValue
     *
     * @return boolean
     */
    protected function checkSavedValue()
    {
        $savedValue = $this->callFormMethod('getSavedData', [$this->getName()]);

        return null !== $savedValue && false !== $savedValue;
    }

    /**
     * prepareAttributes
     *
     * @param array $attrs Field attributes to prepare
     *
     * @return array
     */
    protected function prepareAttributes(array $attrs)
    {
        $attrs = parent::prepareAttributes($attrs);
        if ($this->isChecked()) {
            $attrs['checked'] = 'checked';
        }

        return $attrs;
    }

    /**
     * Get default value
     *
     * @return string
     */
    protected function getDefaultValue()
    {
        return parent::getDefaultValue() ?: '1';
    }

    /**
     * Get default value
     *
     * @return string
     */
    protected function getDefaultHiddenValue()
    {
        return '';
    }

    /**
     * Return field template
     *
     * @return string
     */
    protected function getFieldTemplate()
    {
        return 'checkbox.twig';
    }
}
