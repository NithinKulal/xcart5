<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Input\Text;

/**
 * Float
 */
class FloatInput extends \XLite\View\FormField\Input\Text\Base\Numeric
{
    /**
     * Widget param names
     */
    const PARAM_E = 'e';
    const PARAM_THOUSAND_SEPARATOR = 'thousand_separator';
    const PARAM_DECIMAL_SEPARATOR  = 'decimal_separator';

    /**
     * Get default E
     *
     * @return integer
     */
    protected static function getDefaultE()
    {
        return 2;
    }

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'form_field/input/text/float.js';

        return $list;
    }

    /**
     * Get value
     *
     * @return float
     */
    public function getValue()
    {
        return $this->sanitizeFloat(parent::getValue());
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
            static::PARAM_E => new \XLite\Model\WidgetParam\TypeInt(
                'Number of digits after the decimal separator',
                static::getDefaultE()
            ),
            static::PARAM_THOUSAND_SEPARATOR => new \XLite\Model\WidgetParam\TypeString(
                'Thousand separator',
                \XLite\Core\Config::getInstance()->Units->thousand_delim
            ),
            static::PARAM_DECIMAL_SEPARATOR => new \XLite\Model\WidgetParam\TypeString(
                'Decimal separator',
                \XLite\Core\Config::getInstance()->Units->decimal_delim
            ),
        );
    }

    /**
     * Sanitize value
     *
     * @return mixed
     */
    protected function sanitize()
    {
        return $this->sanitizeFloat(parent::sanitize());
    }

    /**
     * Sanitize value
     *
     * @param string $value Value
     *
     * @return mixed
     */
    protected function sanitizeFloat($value)
    {
        return round((float) ($value), $this->getParam(self::PARAM_E));
    }

    /**
     * Assemble validation rules
     *
     * @return array
     */
    protected function assembleValidationRules()
    {
        $rules = parent::assembleValidationRules();
        $rules[] = 'custom[number]';

        return $rules;
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
        $classes[] = 'float';

        return $classes;
    }

    /**
     * Get default maximum size
     *
     * @return integer
     */
    protected function getDefaultMaxSize()
    {
        return 15;
    }

    /**
     * getCommonAttributes
     *
     * @return array
     */
    protected function getCommonAttributes()
    {
        $attributes = parent::getCommonAttributes();

        $attributes['data-decimal-delim']  = $this->getParam(self::PARAM_DECIMAL_SEPARATOR);
        $attributes['data-thousand-delim'] = $this->getParam(self::PARAM_THOUSAND_SEPARATOR);

        $e = $this->getE();
        if (isset($e)) {
            $attributes['data-e'] = $e;
        }

        return $attributes;
    }

    /**
     * Get mantis
     *
     * @return integer
     */
    protected function getE()
    {
        return $this->getParam(static::PARAM_E);
    }
}
