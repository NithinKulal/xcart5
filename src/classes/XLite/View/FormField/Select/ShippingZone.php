<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Select;

/**
 * Sipping zone selector
 */
class ShippingZone extends \XLite\View\FormField\Select\Regular
{
    const PARAM_METHOD = 'usedZones';
    const SEPARATOR_ID = 'SEPARATOR';
    const SEPARATOR_SIGN = '&#x2500;';

    /**
     * Get zones list
     *
     * @return array
     */
    protected function getZonesList()
    {
        $list = array();
        foreach (\XLite\Core\Database::getRepo('XLite\Model\Zone')->findAllZones() as $e) {
            $list[$e->getZoneId()] = $e->getZoneName();
        }

        return $list;
    }

    /**
     * Get default options
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return $this->getZonesList();
    }

    /**
     * getOptions
     *
     * @return array
     */
    protected function getOptions()
    {
        $list = parent::getOptions();

        $method = $this->getParam(static::PARAM_METHOD);
        if ($method) {
            $zones = \XLite\Core\Database::getRepo('XLite\Model\Zone')->getOfflineShippingZones($method);

            if ($zones[0]) {
                $list = $zones[0];
                $list += array(static::SEPARATOR_ID => str_repeat(static::SEPARATOR_SIGN, 10));
                $list += $zones[1];
            }
        }

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
            static::PARAM_METHOD => new \XLite\Model\WidgetParam\TypeObject(
                'Shipping method',
                null,
                false,
                'XLite\Model\Shipping\Method'
            ),
        );
    }

    /**
     * Check - specified option is disabled or not
     *
     * @param mixed $value Option value
     *
     * @return boolean
     */
    protected function isOptionDisabled($value)
    {
        return static::SEPARATOR_ID === $value
            ? true
            : parent::isOptionDisabled($value);
    }

    /**
     * Assemble classes
     *
     * @param array $classes Classes
     *
     * @return array
     */
    public function assembleClasses(array $classes)
    {
        $classes = parent::assembleClasses($classes);
        $classes[] = 'not-significant';

        return $classes;
    }
}
