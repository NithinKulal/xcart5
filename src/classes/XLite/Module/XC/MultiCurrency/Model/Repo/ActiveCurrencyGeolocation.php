<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MultiCurrency\Model\Repo;


/**
 * ActiveCurrency repository
 * @Decorator\Depend ("XC\Geolocation")
 */
class ActiveCurrencyGeolocation extends \XLite\Module\XC\MultiCurrency\Model\Repo\ActiveCurrency implements \XLite\Base\IDecorator
{
    /**
     * Get default currency
     *
     * @return \XLite\Model\Currency
     */
    public function getDefaultCurrency()
    {
        $data = \XLite\Module\XC\Geolocation\Logic\Geolocation::getInstance()->getLocation(new \XLite\Module\XC\Geolocation\Logic\GeoInput\IpAddress());

        $defaultCurrencyId = \XLite\Core\Config::getInstance()->General->shop_currency;
        if (isset($data['country'])) {
            $country = \XLite\Core\Database::getRepo('XLite\Model\Country')->find($data['country']);

            if ($country) {
                if ($country->getActiveCurrency()) {
                    $defaultCurrencyId = $country->getActiveCurrency()->getCurrency()->getCurrencyId();
                } elseif ($country->getCurrency() && $country->getCurrency()->isActiveMultiCurrency()) {
                    $defaultCurrencyId = $country->getCurrency()->getCurrencyId();
                }
            }
        }

        $defaultCurrency = \XLite\Core\Database::getRepo('XLite\Model\Currency')
            ->find($defaultCurrencyId);

        return $defaultCurrency;
    }

    /**
     * Get default country code
     *
     * @return \XLite\Model\Country
     */
    public function getDefaultCountry()
    {
        $country = null;

        $data = \XLite\Module\XC\Geolocation\Logic\Geolocation::getInstance()->getLocation(new \XLite\Module\XC\Geolocation\Logic\GeoInput\IpAddress());

        if (isset($data['country'])) {
            $country = \XLite\Core\Database::getRepo('XLite\Model\Country')->find($data['country']);
        }

        return $country ?: parent::getDefaultCountry();
    }
}