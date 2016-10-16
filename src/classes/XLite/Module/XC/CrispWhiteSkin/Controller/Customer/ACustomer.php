<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CrispWhiteSkin\Controller\Customer;

use XLite\Module\XC\CrispWhiteSkin;

class ACustomer extends \XLite\Controller\Customer\ACustomer implements \XLite\Base\IDecorator
{
    /**
     * Get current selected country if available
     *
     * @return \XLite\Model\Country
     */
    public function getCurrentCountry()
    {
        $country = null;

        if (CrispWhiteSkin\Main::isModuleEnabled('XC\MultiCurrency')) {
            $country = \XLite\Module\XC\MultiCurrency\Core\MultiCurrency::getInstance()->getSelectedCountry();
        } elseif (CrispWhiteSkin\Main::isModuleEnabled('XC\Geolocation')) {
            $address = $this->getCart() && $this->getCart()->getProfile() && $this->getCart()->getProfile()->getShippingAddress()
                ? $this->getCart()->getProfile()->getShippingAddress()
                : null;

            if (!$address) {
                $country = \XLite\Model\Address::getDefaultFieldValue('country');
            } else {
                $country = $address->getCountry();
            }
        }

        return $country;
    }

    /**
     * Get current selected currency if available
     *
     * @return \XLite\Model\Currency
     */
    public function getCurrentCurrency()
    {
        $currency = null;

        if (CrispWhiteSkin\Main::isModuleEnabled('XC\MultiCurrency')) {
            $currency = \XLite\Module\XC\MultiCurrency\Core\MultiCurrency::getInstance()->getSelectedMultiCurrency();
        }

        return $currency;
    }

    /**
     * Return true if there are active currencies for currency selector
     *
     * @return boolean
     */
    public function isCurrencySelectorAvailable()
    {
        $result = false;

        if (CrispWhiteSkin\Main::isModuleEnabled('XC\MultiCurrency')) {
            $result = \XLite\Module\XC\MultiCurrency\Core\MultiCurrency::getInstance()->hasMultipleCurrencies();
        }

        return $result;
    }
}
