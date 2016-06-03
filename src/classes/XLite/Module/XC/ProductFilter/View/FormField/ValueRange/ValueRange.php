<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductFilter\View\FormField\ValueRange;

/**
 * Value range
 *
 */
class ValueRange extends \XLite\View\FormField\AFormField
{
    /**
     * Widget param names
     */
    const PARAM_MIN_VALUE = 'minValue';
    const PARAM_MAX_VALUE = 'maxValue';
    const PARAM_UNIT      = 'unit';
    const PARAM_IS_OPENED = 'isOpened';

    /**
     * Return field type
     *
     * @return string
     */
    public function getFieldType()
    {
        return self::FIELD_TYPE_TEXT;
    }

    /**
     * Return min value
     *
     * @return string
     */
    public function getMinValue()
    {
        return $this->getParam(self::PARAM_MIN_VALUE);
    }

    /**
     * Return max value
     *
     * @return string
     */
    public function getMaxValue()
    {
        return $this->getParam(self::PARAM_MAX_VALUE);
    }

    /**
     * Return unit
     *
     * @return string
     */
    public function getUnit()
    {
        return $this->getParam(self::PARAM_UNIT);
    }

    /**
     * Return range value
     *
     * @param integer $index Index
     *
     * @return string
     */
    public function getRangeValue($index)
    {
        $value = $this->getValue();

        return is_array($value) && isset($value[$index])
            ? $value[$index]
            : '';
    }

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = $this->getDir() . '/style.css';

        return $list;
    }

    /**
     * Label only
     *
     * @return array
     */
    public function displayLabelOnly()
    {
        return false;
    }

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = $this->getDir() . '/script.js';

        return $list;
    }

    /**
     * Is opened
     *
     * @return boolean
     */
    protected function isOpened()
    {
        $value = $this->getValue();

        return $this->getParam(self::PARAM_IS_OPENED)
            || (
                $value
                && is_array($value)
                && ($value[0] || $value[1])
            );
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
            static::PARAM_MIN_VALUE => new \XLite\Model\WidgetParam\TypeFloat('Min. value', 0),
            static::PARAM_MAX_VALUE => new \XLite\Model\WidgetParam\TypeFloat('Max. value', 99999),
            static::PARAM_UNIT      => new \XLite\Model\WidgetParam\TypeString('Unit', ''),
            static::PARAM_IS_OPENED => new \XLite\Model\WidgetParam\TypeBool('Is opened', false),
        );
    }

    /**
     * Return field template
     *
     * @return string
     */
    protected function getFieldTemplate()
    {
        return 'input.twig';
    }

    /**
     * Return name of the folder with templates
     *
     * @return string
     */
    protected function getDir()
    {
        return 'modules/XC/ProductFilter/form_field/value_range';
    }

    /**
     * Get label container class
     *
     * @return string
     */
    protected function getLabelContainerClass()
    {
        return parent::getLabelContainerClass()
            . $this->getCommonClass();
    }

    /**
     * Get value container class
     *
     * @return string
     */
    protected function getValueContainerClass()
    {
        return parent::getValueContainerClass()
            . $this->getCommonClass();
    }

    /**
     * Get value container class
     *
     * @return string
     */
    protected function getCommonClass()
    {
        $class = ' collapsible';
        
        if (!$this->isOpened()) {
            $class .= ' collapsed';
        }

        return $class;
    }
}
