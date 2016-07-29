<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Geolocation\Controller\Customer;

/**
 * @Decorator\Depend ("XC\MultiCurrency")
 */
class LocationSelectMultiCurrency extends \XLite\Module\XC\Geolocation\Controller\Customer\LocationSelect implements \XLite\Base\IDecorator
{
    /**
     * Set estimate destination
     *
     * @return void
     */
    protected function doActionChangeLocation()
    {
        $countryCode = \XLite\Core\Request::getInstance()->address_country;

        $country = (!$countryCode && !$this->hasField('country_code'))
            ? \XLite\Model\Address::getDefaultFieldValue('country')
            : $country = \XLite\Core\Database::getRepo('XLite\Model\Country')->find($countryCode);

        $changeCountry = isset($country)
            && $country->getEnabled();

        if ($changeCountry) {
            $this->setHardRedirect(true);
            \XLite\Module\XC\MultiCurrency\Core\MultiCurrency::getInstance()->setSelectedCountry($country);
        }

        parent::doActionChangeLocation();
    }
}
