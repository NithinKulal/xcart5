<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MultiCurrency\View\LanguageSelector;

use XLite\Module\XC\MultiCurrency\Core\MultiCurrency;

/**
 * Language selector (customer)
 */
class Customer extends \XLite\View\LanguageSelector\Customer implements \XLite\Base\IDecorator
{
    /**
     * Get JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $return = parent::getJSFiles();

        if (MultiCurrency::getInstance()->hasMultipleCurrencies()) {
            $return[] = $this->getDir() . LC_DS . 'script.js';
            $return[] = $this->getDir() . LC_DS . 'select.js';
        }

        return $return;
    }

    /**
     * Return widget directory
     *
     * @return string
     */
    protected function getDir()
    {
        if (MultiCurrency::getInstance()->hasMultipleCurrencies()) {
            $return = 'modules' . LC_DS . 'XC' . LC_DS . 'MultiCurrency' . LC_DS . 'language_selector';
        } else {
            $return = parent::getDir();
        }

        return $return;
    }

    /**
     * Get selected currency code
     *
     * @return string
     */
    protected function getSelectedCurrencyCode()
    {
        return MultiCurrency::getInstance()->getSelectedCurrency()->getCode();
    }

    /**
     * Check if there is more than one active currency
     *
     * @return boolean
     */
    protected function hasAvailableCountries()
    {
        return MultiCurrency::getInstance()->hasAvailableCountries();
    }

    /**
     * Check if there is more than one active currency
     *
     * @return boolean
     */
    protected function hasMultipleCurrencies()
    {
        return MultiCurrency::getInstance()->hasMultipleCurrencies();
    }

    /**
     * Check if there is more than one
     *
     * @return boolean
     */
    protected function hasMultipleLanguages()
    {
        return 0 < count($this->getActiveLanguages());
    }

    /**
     * Get the list of countries and assigned currencies
     *
     * @return array
     */
    protected function getCountriesByCurrency()
    {
        $return = array();

        $countries = MultiCurrency::getInstance()->getCountriesWithCurrencies();

        if (is_array($countries) && !empty($countries)) {
            foreach($countries as $country) {
                $return[$country->getCode()] = $country->getActiveCurrency()->getCode();
            }
        }

        return $return;
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible() || $this->hasMultipleCurrencies();
    }
}