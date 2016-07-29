<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MultiCurrency\Model;

/**
 * ActiveCurrency model
 * @Decorator\Depend ("XC\Geolocation")
 */
class ActiveCurrencyGeolocation extends \XLite\Module\XC\MultiCurrency\Model\ActiveCurrency implements \XLite\Base\IDecorator
{
    /**
     * Return first assigned country
     *
     * @return \XLite\Model\Country
     */
    public function getFirstCountry()
    {
        $countries = $this->getCountries();

        $data = \XLite\Module\XC\Geolocation\Logic\Geolocation::getInstance()->getLocation(new \XLite\Module\XC\Geolocation\Logic\GeoInput\IpAddress());

        if (isset($data['country'])) {
            foreach ($countries as $country) {
                if ($data['country'] == $country->getCode()) {
                    return $country;
                }
            }
        }

        return parent::getFirstCountry();
    }
}