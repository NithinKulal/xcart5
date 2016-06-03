<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Input\Text\Base;

/**
 * Numeric
 */
abstract class Numeric extends \XLite\View\FormField\Input\Text
{
    /**
     * Widget param names
     */
    const PARAM_MIN              = 'min';
    const PARAM_MAX              = 'max';
    const PARAM_MOUSE_WHEEL_CTRL = 'mouseWheelCtrl';
    const PARAM_MOUSE_WHEEL_ICON = 'mouseWheelIcon';
    const PARAM_ALLOW_EMPTY      = 'allowEmpty';

    /**
     * Prepare request data (typecasting)
     *
     * @param mixed $value Value
     *
     * @return mixed
     */
    public function prepareRequestData($value)
    {
        $result = preg_replace('/[^\d\.-]/Ss', '', parent::prepareRequestData($value));

        if (!$result && !$this->getParam(self::PARAM_ALLOW_EMPTY)) {
            $result = 0;
        }

        return $result;
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
            static::PARAM_MIN              => new \XLite\Model\WidgetParam\TypeInt('Minimum', null),
            static::PARAM_MAX              => new \XLite\Model\WidgetParam\TypeInt('Maximum', null),
            static::PARAM_MOUSE_WHEEL_CTRL => new \XLite\Model\WidgetParam\TypeBool('Mouse wheel control', $this->getDefaultMouseWheelCtrlValue()),
            static::PARAM_MOUSE_WHEEL_ICON => new \XLite\Model\WidgetParam\TypeBool('User mouse wheel icon', $this->getDefaultMouseWheelIconValue()),
            static::PARAM_ALLOW_EMPTY      => new \XLite\Model\WidgetParam\TypeBool('Allow empty not-numeric value', true),
        );
    }

    /**
     * Get default value for mouseWheelCtrl parameter
     *
     * @return boolean
     */
    protected function getDefaultMouseWheelCtrlValue()
    {
        return false;
    }

    /**
     * Get default value for mouseWheelIcon parameter
     *
     * @return boolean
     */
    protected function getDefaultMouseWheelIconValue()
    {
        return true;
    }

    /**
     * Should we select all field content on focus
     *
     * @return boolean
     */
    protected function getDefaultSelectOnFocus()
    {
        return true;
    }

    /**
     * Check field validity
     *
     * @return boolean
     */
    protected function checkFieldValidity()
    {
        $result = parent::checkFieldValidity();

        if ($result) {
            $result = $this->checkRange();
        }

        return $result;
    }

    /**
     * Check range 
     * 
     * @return boolean
     */
    protected function checkRange()
    {
        return $this->checkMinValue() && $this->checkMaxValue();
    }

    /**
     * Check minimum value
     *
     * @return boolean
     */
    protected function checkMinValue()
    {
        $result = true;

        $min = $this->getMinValue();

        if (!is_null($min) && $this->getValue() < $min) {

            $result = false;
            $this->errorMessage = \XLite\Core\Translation::lbl(
                'The value of the X field must be greater than Y',
                array(
                    'name' => $this->getLabel(),
                    'min' => $min,
                )
            );
        }

        return $result;
    }

    /**
     * Check maximum value
     *
     * @return boolean
     */
    protected function checkMaxValue()
    {
        $result = true;

        $max = $this->getMaxValue();

        if (!is_null($max) && $this->getValue() > $max) {

            $result = false;
            $this->errorMessage = \XLite\Core\Translation::lbl(
                'The value of the X field must be less than Y',
                array(
                    'name' => $this->getLabel(),
                    'max' => $max,
                )
            );
        }

        return $result;
    }

    /**
     * Get min value
     *
     * @return integer
     */
    protected function getMinValue()
    {
        return $this->getParam(self::PARAM_MIN);
    }

    /**
     * Get max value
     *
     * @return integer
     */
    protected function getMaxValue()
    {
        return $this->getParam(self::PARAM_MAX);
    }

    /**
     * Assemble validation rules
     *
     * @return array
     */
    protected function assembleValidationRules()
    {
        $rules = parent::assembleValidationRules();

        if (!is_null($this->getParam(self::PARAM_MIN))) {
            $rules[] = 'min[' . $this->getParam(self::PARAM_MIN) . ']';
        }

        if (!is_null($this->getParam(self::PARAM_MAX))) {
            $rules[] = 'max[' . $this->getParam(self::PARAM_MAX) . ']';
        }

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

        if ($this->getParam(static::PARAM_MOUSE_WHEEL_CTRL)) {
            $classes[] = 'wheel-ctrl';
            if (!$this->getParam(static::PARAM_MOUSE_WHEEL_ICON)) {
                $classes[] = 'no-wheel-mark';
            }
        }

        return $classes;
    }

    /**
     * getCommonAttributes
     *
     * @return array
     */
    protected function getCommonAttributes()
    {
        return parent::getCommonAttributes()
            + array(
                'data-min' => $this->getParam(static::PARAM_MIN),
                'data-max' => $this->getParam(static::PARAM_MAX),
            );
    }

}
