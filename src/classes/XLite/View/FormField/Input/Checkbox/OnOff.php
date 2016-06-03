<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Input\Checkbox;

/**
 * On/Off FlipSwitch
 */
class OnOff extends \XLite\View\FormField\Input\Checkbox
{
    /**
     * Widget param names
     */
    const PARAM_FA_CLASS = 'faClass';
    const PARAM_ON_LABEL = 'onLabel';
    const PARAM_OFF_LABEL = 'offLabel';
    const PARAM_DISABLED = 'disabled';

    protected $currentId;

    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = $this->getDir() . '/input/checkbox/on_off.css';

        return $list;
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
        return (bool) parent::prepareRequestData($value);
    }

    /**
     * Register CSS class to use for wrapper block (SPAN) of input field.
     * It is usable to make unique changes of the field.
     *
     * @return string
     */
    public function getWrapperClass()
    {
        return trim(parent::getWrapperClass() . ' ' . ($this->isChecked() ? 'enabled' : 'disabled'));
    }

    /**
     * Return a value for the "id" attribute of the field input tag
     *
     * @return string
     */
    public function getFieldId()
    {
        if (!isset($this->currentId)) {
            $this->currentId = parent::getFieldId();

            if ('' === $this->currentId) {
                $this->currentId = 'onoff' . mt_rand();
            }
        }

        return $this->currentId;
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
            self::PARAM_FA_CLASS => new \XLite\Model\WidgetParam\TypeString('Font awesome', $this->getDefaultFontAwesomeClass()),
            self::PARAM_ON_LABEL => new \XLite\Model\WidgetParam\TypeString('On label', $this->getDefaultOnLabel()),
            self::PARAM_OFF_LABEL => new \XLite\Model\WidgetParam\TypeString('Off label', $this->getDefaultOffLabel()),
            self::PARAM_DISABLED => new \XLite\Model\WidgetParam\TypeBool('Disabled', false),
        );
    }

    /**
     * Returns default param value
     *
     * @return string
     */
    protected function getDefaultFontAwesomeClass()
    {
        return 'fa-check';
    }

    /**
     * Returns param value
     *
     * @return string
     */
    protected function getFontAwesomeClass()
    {
        return $this->getParam(static::PARAM_FA_CLASS);
    }

    /**
     * Returns default param value
     *
     * @return string
     */
    protected function getDefaultOnLabel()
    {
        return 'checkbox.onoff.on';
    }

    /**
     * Returns param value
     *
     * @return string
     */
    protected function getOnLabel()
    {
        return $this->getParam(static::PARAM_ON_LABEL);
    }

    /**
     * Returns default param value
     *
     * @return string
     */
    protected function getDefaultOffLabel()
    {
        return 'checkbox.onoff.off';
    }

    /**
     * Returns param value
     *
     * @return string
     */
    protected function getOffLabel()
    {
        return $this->getParam(static::PARAM_OFF_LABEL);
    }

    /**
     * Determines if checkbox is checked
     *
     * @return boolean
     */
    protected function isChecked()
    {
        return $this->getValue() || $this->checkSavedValue();
    }

    /**
     * Returns disabled state
     *
     * @return boolean
     */
    protected function isDisabled()
    {
        return $this->getParam(static::PARAM_DISABLED);
    }

    /**
     * Return field template
     *
     * @return string
     */
    protected function getFieldTemplate()
    {
        return 'input/checkbox/on_off.twig';
    }

    /**
     * Get default wrapper class
     *
     * @return string
     */
    protected function getDefaultWrapperClass()
    {
        return trim(parent::getDefaultWrapperClass())
            . ' input-field-wrapper';
    }

    /**
     * getCommonAttributes
     *
     * @return array
     */
    protected function getCommonAttributes()
    {
        $list = parent::getCommonAttributes();
        $list['value'] = '1';

        if ($this->isDisabled()) {
            $list['disabled'] = 'disabled';
        }

        return $list;
    }
}
