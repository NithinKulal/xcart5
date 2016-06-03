<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

/**
 * Date picker widget
 *
 * fixme: move to form fields
 */
class DatePicker extends \XLite\View\FormField\Input\Text
{
    /*
     * Constants: names of a widget parameters
     */
    const PARAM_FIELD     = 'field';
    const PARAM_VALUE     = 'value';
    const PARAM_HIGH_YEAR = 'highYear';
    const PARAM_LOW_YEAR  = 'lowYear';

    /**
     * Get element class name
     *
     * @return string
     */
    public function getClassName()
    {
        $name = str_replace(
            array('[', ']'),
            array('-', ''),
            $this->getParam(self::PARAM_FIELD)
        );

        return strtolower($name);
    }

    /**
     * Get widget value as string
     *
     * @return string
     */
    public function getValueAsString()
    {
        $result = '';

        if (0 < (int)$this->getParam(self::PARAM_VALUE)) {

            $formats = \XLite\Core\Converter::getDateFormatsByStrftimeFormat(
                \XLite\Core\Config::getInstance()->Units->date_format
            );
            $format = $formats['phpFormat'];

            $result = date($format, $this->getParam(self::PARAM_VALUE));
        }

        return $result;
    }

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'common/ui.datepicker.css';
        $list[] = $this->getDir() . '/datepicker.css';

        return $list;
    }

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = $this->getDir() . '/datepicker.js';

        return $list;
    }

    /**
     * Set value
     *
     * @param mixed $value Value to set
     *
     * @return void
     */
    public function setValue($value)
    {
        if (!is_numeric($value)) {
            $value = \XLite\Core\Converter::parseFromJsFormat($value);
        }

        parent::setValue($value);
    }

    /**
     * Prepare request data (typecasting)
     *
     * @param mixed $value Value
     *
     * @return mixed
     */
    public function prepareRequestData($value)
    {
        $result = parent::prepareRequestData($value);

        if (!is_numeric($result)) {
            $result = \XLite\Core\Converter::parseFromJsFormat($result);
        }

        return $result;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getFieldTemplate()
    {
        return 'datepicker.twig';
    }

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            self::PARAM_FIELD     => new \XLite\Model\WidgetParam\TypeString('Name of date field prefix', 'date'),
            self::PARAM_VALUE     => new \XLite\Model\WidgetParam\TypeInt('Value of date field (timestamp)', null),
            self::PARAM_HIGH_YEAR => new \XLite\Model\WidgetParam\TypeInt('The high year', date('Y', \XLite\Core\Converter::time()) - 1),
            self::PARAM_LOW_YEAR  => new \XLite\Model\WidgetParam\TypeInt('The low year', 2035),
        );
    }

    /**
     * Return specific for JS code widget options
     *
     * @return array
     */
    protected function getDatePickerOptions()
    {
        $currentFormats = \XLite\Core\Converter::getDateFormatsByStrftimeFormat();
        return array(
            'dateFormat' => $currentFormats['jsFormat'],
            'highYear'   => $this->getParam(static::PARAM_HIGH_YEAR),
            'lowYear'    => $this->getParam(static::PARAM_LOW_YEAR),
        );
    }

    /**
     * Getter for Field-only flag
     *
     * @return boolean
     */
    protected function getDefaultParamFieldOnly()
    {
        return true;
    }
}
