<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Input\Text;

/**
 * Date
 */
class Date extends \XLite\View\FormField\Input\Text
{
    /**
     * Widget param names
     */
    const PARAM_MIN = 'min';
    const PARAM_MAX = 'max';

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = $this->getDir() . '/js/date.js';

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

        $list[] = $this->getDir() . '/input/text/date.css';

        return $list;
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
            self::PARAM_MIN => new \XLite\Model\WidgetParam\TypeInt('Minimum date', null),
            self::PARAM_MAX => new \XLite\Model\WidgetParam\TypeInt('Maximum date', null),
        );
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
        $result = true;

        if (!is_null($this->getParam(self::PARAM_MIN)) && $this->getValue() < $this->getParam(self::PARAM_MIN)) {

            $result = false;
            $this->errorMessage = \XLite\Core\Translation::lbl(
                'The value of the X field must be greater than Y',
                array(
                    'name' => $this->getLabel(),
                    'min'  => $this->formatDate($this->getParam(self::PARAM_MIN)),
                )
            );

        } elseif (!is_null($this->getParam(self::PARAM_MAX)) && $this->getValue() > $this->getParam(self::PARAM_MAX)) {

            $result = false;
            $this->errorMessage = \XLite\Core\Translation::lbl(
                'The value of the X field must be less than Y',
                array(
                    'name' => $this->getLabel(),
                    'max'  => $this->formatDate($this->getParam(self::PARAM_MAX)),
                )
            );

        }

        return $result;
    }

    /**
     * Sanitize value
     *
     * @return integer
     */
    protected function sanitize()
    {
       return parent::sanitize() ?: 0;
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
     * getCommonAttributes
     *
     * @return array
     */
    protected function getCommonAttributes()
    {
        $list = parent::getCommonAttributes();

        if (is_numeric($list['value']) || is_int($list['value'])) {
            $list['value'] = $list['value']
                ? \XLite\Core\Converter::formatDate($list['value'])
                : '';
        }

        return $list;
    }

    /**
     * Register some data that will be sent to template as special HTML comment
     *
     * @return array
     */
    protected function getCommentedData()
    {
        $data = parent::getCommentedData();

        $currentFormats = \XLite\Core\Converter::getDateFormatsByStrftimeFormat();
        $data['dateFormat'] = $currentFormats['jsFormat'];

        return $data;
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

        $classes[] = 'datepicker';

        return $classes;
    }

    /**
     * Get default maximum size
     *
     * @return integer
     */
    protected function getDefaultMaxSize()
    {
        return 50;
    }
}
