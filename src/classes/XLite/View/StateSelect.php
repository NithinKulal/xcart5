<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

// FIXME - to remove

/**
 * State selector
 */
class StateSelect extends \XLite\View\FormField
{
    /**
     * Widget param names
     */

    const PARAM_FIELD_NAME = 'field';
    const PARAM_STATE      = 'state';
    const PARAM_FIELD_ID   = 'fieldId';
    const PARAM_IS_LINKED  = 'isLinked';
    const PARAM_CLASS_NAME = 'className';


    /**
     * States defined falg
     *
     * @var boolean
     */
    protected static $statesDefined = false;


    /**
     * Check - current state is custom state or not
     *
     * @return boolean
     */
    public function isCustomState()
    {
        return !$this->getParam(self::PARAM_STATE) || !$this->getParam(self::PARAM_STATE)->getStateId();
    }

    /**
     * Get current state value
     *
     * @return string
     */
    public function getStateValue()
    {
        return $this->getParam(self::PARAM_STATE) ? $this->getParam(self::PARAM_STATE)->getState() : '';
    }

    /**
     * Check - states list are defined as javascript array or not
     *
     * @return boolean
     */
    public function isDefineStates()
    {
        return $this->getParam(self::PARAM_IS_LINKED) && !self::$statesDefined;
    }

    /**
     * Get countries states
     *
     * @return array
     */
    public function getCountriesStates()
    {
        self::$statesDefined = true;

        return \XLite\Core\Database::getRepo('XLite\Model\Country')->findCountriesStates();
    }

    /**
     * Get javascript data block
     *
     * @return string
     */
    public function getJSDataDefinitionBlock()
    {
        $code = 'var CountriesStates = {};' . "\n";

        foreach ($this->getCountriesStates() as $countryCode => $states) {
            $code .= 'CountriesStates.' . $countryCode . ' = [' . "\n";
            $i = 1;
            $length = count($states);
            foreach ($states as $stateCode => $state) {
                $code .= '{state_code: "' . $stateCode . '", state: "' . $state . '"}'
                    . ($i == $length ? '' : ',')
                    . "\n";
                $i++;
            }
            $code .= '];' . "\n";
        }

        return $code;
    }

    /**
     * Check - specified state is selected or not
     *
     * @param \XLite\Model\State $state Specidied (current) state
     *
     * @return boolean
     */
    public function isStateSelected(\XLite\Model\State $state)
    {
        return $state->getStateId() == $this->getParam(self::PARAM_STATE)->getStateId();
    }


    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'common/select_state.twig';
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
            self::PARAM_FIELD_NAME => new \XLite\Model\WidgetParam\TypeString('Field name', ''),
            self::PARAM_FIELD_ID   => new \XLite\Model\WidgetParam\TypeString('Field ID', ''),
            self::PARAM_STATE      => new \XLite\Model\WidgetParam\TypeObject('Selected state', null, false, '\XLite\Model\State'),
            self::PARAM_CLASS_NAME => new \XLite\Model\WidgetParam\TypeString('Class name', ''),
            self::PARAM_IS_LINKED  => new \XLite\Model\WidgetParam\TypeBool('Linked with country selector', 0),
        );
    }

    /**
     * Return states list
     *
     * @return array
     */
    protected function getStates()
    {
        $states = array();

        if (
            $this->getParam(self::PARAM_STATE)
            && $this->getParam(self::PARAM_STATE)->getCountry()
        ) {
            $states = $this->getParam(self::PARAM_STATE)->getCountry()->getStates();
        }

        return $states;
    }
}
