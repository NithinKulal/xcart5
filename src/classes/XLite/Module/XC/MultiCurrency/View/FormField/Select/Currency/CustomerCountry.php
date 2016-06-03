<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MultiCurrency\View\FormField\Select\Currency;

use XLite\Module\XC\MultiCurrency\Core\MultiCurrency;

/**
 * Customer currency selector
 */
class CustomerCountry extends \XLite\View\FormField\Select\ASelect
{
    /**
     * Return field value
     *
     * @return mixed
     */
    public function getValue()
    {
        return MultiCurrency::getInstance()->getSelectedCountry()->getCode();
    }

    /**
     * Return default options list
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        $return = array();

        $countries = MultiCurrency::getInstance()->getAvailableCountriesAsArray();

        if (
            is_array($countries)
            && !empty($countries)
        ) {
            foreach ($countries as $country) {
                $return[$country['code']] = $country['country'];
            }
        }

        return $return;
    }

    /**
     * getDefaultLabel
     *
     * @return string
     */
    protected function getDefaultLabel()
    {
        return static::t('Country');
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return MultiCurrency::getInstance()->hasAvailableCountries();
    }
}