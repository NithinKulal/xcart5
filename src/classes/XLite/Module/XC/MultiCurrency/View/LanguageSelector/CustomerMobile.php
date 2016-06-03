<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MultiCurrency\View\LanguageSelector;

use XLite\Module\XC\MultiCurrency\Core\MultiCurrency;
use XLite\View\CacheableTrait;

/**
 * Language selector (customer)
 *
 * @ListChild (list="layout.header.mobile.menu", weight="999999", zone="customer")
 */
class CustomerMobile extends \XLite\View\LanguageSelector\Customer
{
    use CacheableTrait;

    /**
     * Get JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $return = parent::getJSFiles();

        $return[] = $this->getDir() . LC_DS . 'script.mobile.js';
        $return[] = $this->getDir() . LC_DS . 'select.js';

        return $return;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->getDir() . '/body.mobile.twig';
    }

    /**
     * Return widget directory
     *
     * @return string
     */
    protected function getDir()
    {
        return 'modules' . LC_DS . 'XC' . LC_DS . 'MultiCurrency' . LC_DS . 'language_selector';
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
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible() || MultiCurrency::getInstance()->hasMultipleCurrencies();
    }

    protected function getCacheParameters()
    {
        $session       = \XLite\Core\Session::getInstance();
        $multicurrency = MultiCurrency::getInstance();

        return array_merge(
            parent::getCacheParameters(),
            [
                $session->getLanguage()->getCode(),
                $multicurrency->getSelectedCountry()->getCode(),
                $multicurrency->getSelectedCurrency()->getCode(),
            ]
        );
    }
}