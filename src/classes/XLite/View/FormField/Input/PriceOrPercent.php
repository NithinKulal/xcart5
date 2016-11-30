<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Input;

/**
 * Price or percent
 */
class PriceOrPercent extends \XLite\View\FormField\Input\AInput
{
    const TYPE_VALUE = 'type';
    const PRICE_VALUE = 'price';
    /**
     * Register CSS class to use for wrapper block of input field.
     * It is usable to make unique changes of the field.
     *
     * @return string
     */
    public function getWrapperClass()
    {
        return parent::getWrapperClass() . ' input-price-or-percent';
    }
    
    /**
     * Return field type
     *
     * @return string
     */
    public function getFieldType()
    {
        return static::FIELD_TYPE_COMPLEX;
    }

    /**
     * Return field template
     *
     * @return string
     */
    protected function getFieldTemplate()
    {
        return 'input/price_or_percent.twig';
    }

    /**
     * Return field value
     *
     * @return mixed
     */
    public function getValue()
    {
        $value = parent::getValue();

        if ($value) {
            if (!is_array($value)) {
                $value = [
                    static::PRICE_VALUE => $value
                ];
            } elseif (!isset($value[static::TYPE_VALUE]) && !isset($value[static::PRICE_VALUE])) {
                $value = [];

                if (isset($value[0])) {
                    $value[static::PRICE_VALUE] = $value[0];
                }

                if (isset($value[1])) {
                    $value[static::TYPE_VALUE] = $value[1];
                }
            }
        }

        return $value;
    }

    /**
     * Return formatted value
     *
     * @return string
     */
    public function getFormattedValue()
    {
        if ($this->getTypeValue() == \XLite\View\FormField\Select\AbsoluteOrPercent::TYPE_PERCENT) {
            return $this->getPriceValue() . \XLite\View\FormField\Select\AbsoluteOrPercent::getInstance()->getPercentTypeLabel();
        } else {
            $currency = \XLite::getInstance()->getCurrency();
            return $currency->getPrefix() . $currency->formatValue($this->getPriceValue()) . $currency->getSuffix();
        }
    }

    // {{{ Type

    /**
     * Returns Type value
     *
     * @return mixed
     */
    protected function getTypeValue()
    {
        $value = $this->getValue();

        return isset($value[static::TYPE_VALUE])
            ? $value[static::TYPE_VALUE]
            : $this->getDefaultTypeValue();
    }

    /**
     * Returns default Type value
     *
     * @return mixed
     */
    protected function getDefaultTypeValue()
    {
        return \XLite\View\FormField\Select\AbsoluteOrPercent::TYPE_ABSOLUTE;
    }

    /**
     * Returns Type widget class
     *
     * @return string
     */
    protected function getTypeWidgetClass()
    {
        return 'XLite\View\FormField\Select\AbsoluteOrPercent';
    }

    /**
     * Returns Type widget params
     *
     * @return array
     */
    protected function getTypeWidgetParams()
    {
        return array(
            static::PARAM_FIELD_ONLY => true,
            static::PARAM_VALUE      => $this->getTypeValue(),
            static::PARAM_NAME       => $this->getName() . '[' . static::TYPE_VALUE . ']',
        );
    }

    /**
     * Returns Type widget class
     *
     * @return string
     */
    protected function getTypeWidget()
    {
        $widget = $this->getChildWidget($this->getTypeWidgetClass(), $this->getTypeWidgetParams());

        return $widget->getContent();
    }

    // }}}

    // {{{ Price

    /**
     * Returns Price value
     *
     * @return mixed
     */
    protected function getPriceValue()
    {
        $value = $this->getValue();

        return isset($value[static::PRICE_VALUE])
            ? $value[static::PRICE_VALUE]
            : $this->getDefaultPriceValue();
    }

    /**
     * Returns default Price value
     *
     * @return mixed
     */
    protected function getDefaultPriceValue()
    {
        return 0;
    }

    /**
     * Returns Price widget class
     *
     * @return string
     */
    protected function getPriceWidgetClass()
    {
        return 'XLite\View\FormField\Input\Text\FloatInput';
    }

    /**
     * Returns Price widget params
     *
     * @return array
     */
    protected function getPriceWidgetParams()
    {
        return array(
            static::PARAM_FIELD_ONLY => true,
            static::PARAM_VALUE => $this->getPriceValue(),
            static::PARAM_NAME       => $this->getName() . '[' . static::PRICE_VALUE . ']',
            \XLite\View\FormField\Input\Text\Base\Numeric::PARAM_MOUSE_WHEEL_ICON => false,

        );
    }

    /**
     * Returns Price widget class
     *
     * @return string
     */
    protected function getPriceWidget()
    {
        $widget = $this->getChildWidget($this->getPriceWidgetClass(), $this->getPriceWidgetParams());

        return $widget->getContent();
    }

    // }}}

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = $this->getDir() . '/input/price_or_percent.js';

        return $list;
    }

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = $this->getDir() . '/input/price_or_percent.css';

        return $list;
    }

    /**
     * Assemble classes
     *
     * @param array $classes Classes
     *
     * @return array
     */
    protected function assembleClasses(array $classes)
    {
        $classes = parent::assembleClasses($classes);
        $classes[] = 'price-or-percent';

        return $classes;
    }
}