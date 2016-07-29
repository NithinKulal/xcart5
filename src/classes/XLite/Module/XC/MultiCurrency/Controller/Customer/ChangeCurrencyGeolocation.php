<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MultiCurrency\Controller\Customer;

/**
 * Change customer currency
 * @Decorator\Depend ("XC\Geolocation")
 */
class ChangeCurrencyGeolocation extends \XLite\Module\XC\MultiCurrency\Controller\Customer\ChangeCurrency implements \XLite\Base\IDecorator
{
    /**
     * Do action 'update'
     *
     * @return void
     */
    protected function doActionUpdate()
    {
        $country = \XLite\Core\Database::getRepo('XLite\Model\Country')->findOneBy(
            array(
                'code' => \XLite\Core\Request::getInstance()->country_code
            )
        );

        $changeCountry = isset($country)
            && $country->getEnabled();

        if ($changeCountry) {
            $address = $this->getCart() && $this->getCart()->getProfile() && $this->getCart()->getProfile()->getShippingAddress()
                ? $this->getCart()->getProfile()->getShippingAddress()
                : null;
            if (!$address) {
                $location = array(
                    'country' => $country->getCode(),
                );

                \XLite\Module\XC\Geolocation\Logic\Geolocation::getInstance()->setCachedLocation($location);

            } else {
                $address->setCountry($country);

                $address->setType(
                    \XLite\Core\Request::getInstance()->address_type ?: \XLite\Core\Config::getInstance()->Shipping->anonymous_address_type
                );

                $address->update();

                $this->updateCart(true);
            }
        }
        
        parent::doActionUpdate();
    }
}