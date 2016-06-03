<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MultiCurrency\Core;

/**
 * Cache decorator
 */
class MultiCurrency extends \XLite\Base
{
    const RATE_UPDATE_INTERVAL  = 3600;

    const RATE_UPDATE_CELL      = 'MultiCurrencyRateUpdateDate';
    const CURRENCY_CODE_CELL    = 'MultiCurrencySelectedCurrencyCode';
    const CURRENCY_ID_CELL      = 'MultiCurrencySelectedCurrencyId';
    const COUNTRY_CODE_CELL     = 'MultiCurrencySelectedCountry';

    /**
     * Has multiple currencies
     *
     * @var boolean
     */
    protected static $hasMultipleCurrencies = null;

    /**
     * Has available countries
     *
     * @var boolean
     */
    protected static $hasAvailableCountries = null;

    /**
     * Available currencies
     *
     * @var \XLite\Module\XC\MultiCurrency\Model\ActiveCurrency[]
     */
    protected static $availableCurrencies = null;

    /**
     * Available countries
     *
     * @var \XLite\Model\Country[]
     */
    protected static $availableCountries = null;

    /**
     * Available countries
     *
     * @var array
     */
    protected static $availableCountriesArray = null;

    /**
     * Countries with currencies
     *
     * @var \XLite\Model\Country[]
     */
    protected static $countriesWithCurrencies = null;

    /**
     * Default currency
     *
     * @var \XLite\Model\Currency
     */
    protected static $defaultCurrency = null;

    /**
     * Default country
     *
     * @var \XLite\Model\Country
     */
    protected static $defaultCountry = null;

    /**
     * Selected currency
     *
     * @var \XLite\Model\Currency
     */
    protected static $selectedCurrency = null;

    /**
     * Selected country
     *
     * @var \XLite\Model\Country
     */
    protected static $selectedCountry = null;

    /**
     * Selected active currency
     *
     * @var \XLite\Module\XC\MultiCurrency\Model\ActiveCurrency
     */
    protected static $selectedMultiCurrency = null;

    /**
     * Convert value by provided rate
     *
     * @param float   $value     Value
     * @param float   $rate      Rate
     * @param integer $precision Precision OPTIONAL
     *
     * @return float
     */
    public function convertValueByRate($value, $rate, $precision = 0)
    {
        if (0 == $precision) {
            $return = (float)($value * $rate);
        } else {
            $return = (float) round((float)($value * $rate), $precision);
        }

        return $return;
    }

    /**
     * Check if the currency currency is default currency
     *
     * @return boolean
     */
    public function isDefaultCurrencySelected()
    {
        $defaultCurrency = $this->getDefaultCurrency();

        $selectedCurrency = $this->getSelectedCurrency();

        return $selectedCurrency->getCode() == $defaultCurrency->getCode();
    }

    /**
     * Check if there is more than one active currency
     *
     * @return boolean
     */
    public function hasMultipleCurrencies()
    {
        if (is_null(static::$hasMultipleCurrencies)) {
            static::$hasMultipleCurrencies = \XLite\Core\Database::getRepo('XLite\Module\XC\MultiCurrency\Model\ActiveCurrency')
                ->hasMultipleCurrencies();
        }

        return static::$hasMultipleCurrencies;
    }

    /**
     * Check if active currencies has available countries
     *
     * @return boolean
     */
    public function hasAvailableCountries()
    {
        if (is_null(static::$hasAvailableCountries)) {
            static::$hasAvailableCountries = \XLite\Core\Database::getRepo('XLite\Module\XC\MultiCurrency\Model\ActiveCurrency')
                ->hasEnabledCountries();
        }

        return static::$hasAvailableCountries;
    }

    /**
     * Get available currencies
     *
     * @return \XLite\Module\XC\MultiCurrency\Model\ActiveCurrency[]
     */
    public function getAvailableCurrencies()
    {
        if (is_null(static::$availableCurrencies)) {
            static::$availableCurrencies = \XLite\Core\Database::getRepo('XLite\Module\XC\MultiCurrency\Model\ActiveCurrency')
                ->getAvailableCurrencies();
        }

        return static::$availableCurrencies;
    }

    /**
     * Get available countries
     *
     * @return \XLite\Model\Country[]
     */
    public function getAvailableCountries()
    {
        if (is_null(static::$availableCountries)) {
            $cnd = new \XLite\Core\CommonCell();

            $cnd->{\XLite\Model\Repo\Country::P_ORDER_BY} = array('translations.country');
            $cnd->{\XLite\Model\Repo\Country::P_ENABLED} = true;

            static::$availableCountries = \XLite\Core\Database::getRepo('XLite\Model\Country')
                ->search($cnd);
        }

        return static::$availableCountries;
    }

    /**
     * Get available countries
     *
     * @return array
     */
    public function getAvailableCountriesAsArray()
    {
        if (is_null(static::$availableCountriesArray)) {
            static::$availableCountriesArray = \XLite\Core\Database::getRepo('XLite\Model\Country')
                ->getAllAvailableCountriesAsArray();
        }

        return static::$availableCountriesArray;
    }

    /**
     * Get countries with assigned currencies
     *
     * @return \XLite\Model\Country[]
     */
    public function getCountriesWithCurrencies()
    {
        if (is_null(static::$countriesWithCurrencies)) {
            static::$countriesWithCurrencies = \XLite\Core\Database::getRepo('XLite\Module\XC\MultiCurrency\Model\ActiveCurrency')
                ->getAvailableCountries();
        }

        return static::$countriesWithCurrencies;
    }

    /**
     * Get default currency
     *
     * @return \XLite\Model\Currency
     */
    public function getDefaultCurrency()
    {
        if (is_null(static::$defaultCurrency)) {
            static::$defaultCurrency = \XLite\Core\Database::getRepo('XLite\Module\XC\MultiCurrency\Model\ActiveCurrency')
                ->getDefaultCurrency();
        }

        return static::$defaultCurrency;
    }

    /**
     * Get default country
     *
     * @return \XLite\Model\Country
     */
    public function getDefaultCountry()
    {
        if (is_null(static::$defaultCountry)) {
            if (
                !\XLite::isAdminZone()
                && !is_null(\XLite\Core\Auth::getInstance()->getProfile())
                && !is_null(\XLite\Core\Auth::getInstance()->getProfile()->getFirstAddress())
                && !is_null(\XLite\Core\Auth::getInstance()->getProfile()->getFirstAddress()->getCountry())
                && !is_null(
                    \XLite\Core\Auth::getInstance()->getProfile()->getFirstAddress()->getCountry()->getActiveCurrency()
                )
            ) {
                static::$defaultCountry = \XLite\Core\Auth::getInstance()->getProfile()->getFirstAddress()->getCountry();
            } else {
                static::$defaultCountry = \XLite\Core\Database::getRepo('XLite\Module\XC\MultiCurrency\Model\ActiveCurrency')
                    ->getDefaultCountry();
            }
        }

        return static::$defaultCountry;
    }

    /**
     * Get selected currency
     *
     * @return \XLite\Model\Currency
     */
    public function getSelectedCurrency()
    {
        if (is_null(static::$selectedCurrency)) {
            $selectedCurrency = \XLite\Core\Database::getRepo('XLite\Module\XC\MultiCurrency\Model\ActiveCurrency')
                ->getCurrencyByCode($this->getSelectedCurrencyCode());

            if (!isset($selectedCurrency)) {
                $selectedCurrency = $this->getDefaultCurrency();
                $this->setSelectedCurrency($selectedCurrency);
            } else {
                $selectedCurrency = $selectedCurrency->getCurrency();
            }

            static::$selectedCurrency = $selectedCurrency;
        }

        return static::$selectedCurrency;
    }

    /**
     * Get selected country
     *
     * @return \XLite\Model\Country
     */
    public function getSelectedCountry()
    {
        if (is_null(static::$selectedCountry)) {
            $selectedCountry = \XLite\Core\Database::getRepo('XLite\Model\Country')->findOneBy(
                array(
                    'code' => $this->getSelectedCountryCode()
                )
            );

            if (!isset($selectedCountry)) {
                $selectedCurrency = $this->getSelectedCurrency();

                if (
                    !isset($selectedCurrency)
                    || !$selectedCurrency->getActiveCurrency()->hasAssignedCountries()
                ) {
                    $selectedCountry = $this->getDefaultCountry();
                } else {
                    $selectedCountry = $selectedCurrency->getActiveCurrency()->getFirstCountry();
                }

                $this->setSelectedCountry($selectedCountry);
            }

            static::$selectedCountry = $selectedCountry;
        }

        return static::$selectedCountry;
    }

    /**
     * Get selected active currency
     *
     * @return \XLite\Module\XC\MultiCurrency\Model\ActiveCurrency
     */
    public function getSelectedMultiCurrency()
    {
        if (is_null(static::$selectedMultiCurrency)) {
            $selectedCurrency = $this->getSelectedCurrency();

            if (!isset($selectedCurrency)) {
                $selectedCurrency = $this->getDefaultCurrency();
            }

            static::$selectedMultiCurrency = $selectedCurrency->getActiveCurrency();
        }

        return static::$selectedMultiCurrency;
    }

    /**
     * Set selected currency
     *
     * @param \XLite\Model\Currency $currency Currency
     *
     * @return void
     */
    public function setSelectedCurrency(\XLite\Model\Currency $currency)
    {
        static::$selectedCurrency = null;
        static::$selectedMultiCurrency = null;

        if (isset($currency)) {
            $this->setSelectedCurrencyCode($currency->getCode());
            $this->setSelectedCurrencyId($currency->getCurrencyId());
        }
    }

    /**
     * Set selected country
     *
     * @param \XLite\Model\Country $country Country
     *
     * @return void
     */
    public function setSelectedCountry(\XLite\Model\Country $country)
    {
        static::$selectedCountry = null;

        if (isset($country)) {
            $this->setSelectedCountryCode($country->getCode());
        }
    }

    /**
     * Check if rates need to be updated
     *
     * @return boolean
     */
    public function needRateUpdate()
    {
        return $this->hasMultipleCurrencies()
        && \XLite\Module\XC\MultiCurrency\Core\CurrencyRate::PROVIDER_NONE
        != \XLite\Core\Config::getInstance()->XC->MultiCurrency->rateProvider;
    }

    /**
     * Update currency rates
     *
     * @return void
     */
    public function updateRates()
    {
        if ($this->needRateUpdate()) {
            \XLite\Core\Database::getRepo('XLite\Module\XC\MultiCurrency\Model\ActiveCurrency')->updateRates();
        }
    }

    /**
     * Set selected currency
     *
     * @return string
     */
    protected function getSelectedCurrencyCode()
    {
        return \XLite\Core\Session::getInstance()->get(static::CURRENCY_CODE_CELL);
    }

    /**
     * Set selected currency
     *
     * @return string
     */
    protected function getSelectedCurrencyId()
    {
        return \XLite\Core\Session::getInstance()->get(static::CURRENCY_ID_CELL);
    }

    /**
     * Set selected currency
     *
     * @return string
     */
    protected function getSelectedCountryCode()
    {
        return \XLite\Core\Session::getInstance()->get(static::COUNTRY_CODE_CELL);
    }

    /**
     * Set next rate update date
     *
     * @return integer
     */
    protected function getRateUpdateDate()
    {
        return \XLite\Core\Session::getInstance()->get(static::RATE_UPDATE_CELL);
    }

    /**
     * Set selected currency
     *
     * @param string $currencyCode Currency code
     *
     * @return void
     */
    protected function setSelectedCurrencyCode($currencyCode)
    {
        \XLite\Core\Session::getInstance()->set(
            static::CURRENCY_CODE_CELL,
            $currencyCode
        );
    }

    /**
     * Set selected currency
     *
     * @param integer $currencyId Currency ID
     *
     * @return void
     */
    protected function setSelectedCurrencyId($currencyId)
    {
        \XLite\Core\Session::getInstance()->set(
            static::CURRENCY_ID_CELL,
            $currencyId
        );
    }

    /**
     * Set selected currency
     *
     * @param string $countryCode Country code
     *
     * @return void
     */
    protected function setSelectedCountryCode($countryCode)
    {
        \XLite\Core\Session::getInstance()->set(
            static::COUNTRY_CODE_CELL,
            $countryCode
        );
    }

    /**
     * Set next rate update date
     *
     * @param integer $date Date
     *
     * @return void
     */
    protected function setRateUpdateDate($date)
    {
        \XLite\Core\Session::getInstance()->set(
            static::RATE_UPDATE_CELL,
            $date
        );
    }
}