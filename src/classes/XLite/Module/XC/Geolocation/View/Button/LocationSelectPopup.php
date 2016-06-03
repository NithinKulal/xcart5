<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Geolocation\View\Button;

use XLite\Module\XC\Geolocation\Logic\GeoInput\IpAddress;
use XLite\Module\XC\Geolocation\Logic\Geolocation;
use XLite\View\CacheableTrait;

/**
 * Trial notice popup button
 *
 * @ListChild (list="layout.header.bar", weight="110", zone="customer")
 */
class LocationSelectPopup extends \XLite\View\Button\APopupButton
{
    use CacheableTrait;

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        return array_merge(
            parent::getJSFiles(),
            array(
                'form_field/select_country.js',
                'modules/XC/Geolocation/location_popup/controller.js',
            )
        );
    }

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        return array_merge(
            parent::getCSSFiles(),
            array(
                'form_field/form_field.css',
                'modules/XC/Geolocation/location_popup/style.css',
            )
        );
    }

    /**
     * Return default button label
     *
     * @return string
     */
    protected function getDefaultLabel()
    {
        return 'My location';
    }

    protected function getLocation()
    {
        $address = $this->getCart() && $this->getCart()->getProfile() && $this->getCart()->getProfile()->getShippingAddress()
            ? $this->getCart()->getProfile()->getShippingAddress()
            : null;

        if (!$address) {
            $country = \XLite\Model\Address::getDefaultFieldValue('country');
        } else {
            $country = $address->getCountry();
        }
        return $country
            ? $country->getCountry()
            : '';
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/XC/Geolocation/location_popup/button.twig';
    }

    /**
     * Return URL parameters to use in AJAX popup
     *
     * @return array
     */
    protected function prepareURLParams()
    {
        return array(
            'target' => 'location_select',
            'widget' => 'XLite\Module\XC\Geolocation\View\LocationSelect',
            'returnUrl' => \XLite\Core\URLManager::getCurrentURL(),
        );
    }

    /**
     * Return CSS classes
     *
     * @return string
     */
    protected function getClass()
    {
        return 'btn location-select';
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return (bool) \XLite\Core\Config::getInstance()->XC->Geolocation->display_location_popup
            && parent::isVisible()
            && !$this->isCheckoutLayout();
    }

    protected function getCacheParameters()
    {
        $params = parent::getCacheParameters();

        $geoLocation = \XLite\Model\Address::shouldAccessLocation()
            ? Geolocation::getInstance()->getLocation(new IpAddress)
            : null;

        $params[] = serialize($geoLocation);

        $address = $this->getCart() && $this->getCart()->getProfile() && $this->getCart()->getProfile()->getShippingAddress()
            ? $this->getCart()->getProfile()->getShippingAddress()
            : null;

        if ($address && $address->getCountry()) {
            $params[] = $address->getCountry()->getCountry();
        }

        return $params;
    }
}