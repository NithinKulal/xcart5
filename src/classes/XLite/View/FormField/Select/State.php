<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Select;

/**
 * \XLite\View\FormField\Select\State
 */
class State extends \XLite\View\FormField\Select\Regular
{
    /**
     * Widget param names
     */
    const PARAM_COUNTRY          = 'country';
    const PARAM_SELECT_ONE       = 'selectOne';
    const PARAM_SELECT_ONE_LABEL = 'selectOneLabel';

    /**
     * Define widget params
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            static::PARAM_COUNTRY          => new \XLite\Model\WidgetParam\TypeString('Country', ''),
            static::PARAM_SELECT_ONE       => new \XLite\Model\WidgetParam\TypeBool('Display Select one option', false),
            static::PARAM_SELECT_ONE_LABEL => new \XLite\Model\WidgetParam\TypeString('Select one label', $this->getDefaultSelectOneLabel()),
        );
    }

    /**
     * Default 'Select one' label
     *
     * @return string
     */
    protected function getDefaultSelectOneLabel()
    {
        return static::t('Select one');
    }

    /**
     * Return field template
     *
     * @return string
     */
    protected function getFieldTemplate()
    {
        return 'select_state.twig';
    }

    /**
     * getDefaultOptions
     *
     * @return array
     */
    protected function getOptions()
    {
        $result = $this->getParam(static::PARAM_OPTIONS);
        if (!$result) {

            if ($this->getParam(static::PARAM_COUNTRY)) {
                $result = \XLite\Core\Database::getRepo('\XLite\Model\State')->findByCountryCodeGroupedByRegion(
                    $this->getParam(static::PARAM_COUNTRY)
                );

            } else {
                $result = \XLite\Core\Database::getRepo('\XLite\Model\State')->findAllStates();
            }
        }

        return $result;
    }

    /**
     * getDefaultOptions
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return array();
    }

    /**
     * Check - current value is selected or not
     *
     * @param mixed $value Value
     *
     * @return boolean
     */
    protected function isStateOptionSelected($state)
    {
        $result = false;

        $fieldValue = $this->getValue();

        $stateId = null;

        if ($fieldValue && $state) {
            $stateId = $state->getStateId();
            $result = $this->isOptionSelected($stateId);

        } else {
            $result = empty($state);
        }

        return $result;
    }
}
