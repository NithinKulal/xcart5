<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Geolocation\View;

/**
 * Shipping estimator
 */
class LocationSelect extends \XLite\View\AView
{
    /**
     * Return list of targets allowed for this widget
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $result = parent::getAllowedTargets();

        $result[] = 'location_select';

        return $result;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/XC/Geolocation/location_popup/body.twig';
    }

    /**
     * Get countries list
     *
     * @return array(\XLite\Model\Country)
     */
    protected function getCountries()
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Country')
            ->findByEnabled(true);
    }

    /**
     * Return true if state selector field is visible
     *
     * @return boolean
     */
    protected function isStateFieldVisible()
    {
        return $this->checkStateFieldVisibility();
    }

    /**
     * Return true if custom_state input field is visible
     *
     * @return boolean
     */
    protected function isCustomStateFieldVisible()
    {
        return $this->checkStateFieldVisibility(true);
    }

    /**
     * Common method to check visibility of state fields
     *
     * @param boolean $isCustom Flag: true - check for custom_state field visibility, false - state selector field
     *
     * @return boolean
     */
    protected function checkStateFieldVisibility($isCustom = false)
    {
        $result = false;

        // hasField() method is defined in controller XLite\Controller\Customer\ShippingEstimate
        if ($this->hasField('state_id')) {

            $address = $this->getAddress();

            $country = !empty($address['country'])
                ? \XLite\Core\Database::getRepo('XLite\Model\Country')->findOneByCode($address['country'])
                : \XLite\Model\Address::getDefaultFieldValue('country');

            $result = $isCustom
                ? !$country || !$country->hasStates()
                : $country && $country->hasStates();
        }

        return $result;
    }

    /**
     * Get selected country code
     *
     * @return string
     */
    protected function getCountryCode()
    {
        $address = $this->getAddress();

        $c = 'US';

        if ($address && isset($address['country'])) {
            $c = $address['country'];

        } elseif (\XLite\Model\Address::getDefaultFieldValue('country')) {
            $c = \XLite\Model\Address::getDefaultFieldValue('country')->getCode();
        }

        return $c;
    }

    /**
     * Get state
     *
     * @return \XLite\Model\State
     */
    protected function getState()
    {
        $address = $this->getAddress();

        $state = null;

        // From getDestinationAddress()
        if ($address && isset($address['state']) && $address['state']) {
            $state = \XLite\Core\Database::getRepo('XLite\Model\State')->findOneByCode($address['state']);

        } elseif (
            $this->getCart()->getProfile()
            && $this->getCart()->getProfile()->getShippingAddress()
            && $this->getCart()->getProfile()->getShippingAddress()->getState()
        ) {

            // From shipping address
            $state = $this->getCart()->getProfile()->getShippingAddress()->getState();

        } elseif (
            !$address
            && \XLite\Model\Address::getDefaultFieldValue('custom_state')
        ) {

            // From config
            $state = new \XLite\Model\State();
            $state->setState(\XLite\Model\Address::getDefaultFieldValue('custom_state'));

        }

        return $state;
    }

    /**
     * Get state
     *
     * @return \XLite\Model\State
     */
    protected function getOtherState()
    {
        $state = null;

        $address = $this->getAddress();

        if (isset($address) && isset($address['custom_state'])) {
            $state = $address['custom_state'];

        } elseif (
            $this->getCart()->getProfile()
            && $this->getCart()->getProfile()->getShippingAddress()
            && $this->getCart()->getProfile()->getShippingAddress()->getCustomState()
        ) {
            // From shipping address
            $state = $this->getCart()->getProfile()->getShippingAddress()->getCustomState();
        }

        return $state;
    }

    /**
     * Get ZIP code
     *
     * @return string
     */
    protected function getZipcode()
    {
        $address = $this->getAddress();

        return ($address && isset($address['zipcode']))
            ? $address['zipcode']
            : '';
    }

    /**
     * Get address type code
     *
     * @return string
     */
    protected function getType()
    {
        $address = $this->getAddress();

        return ($address && isset($address['type']))
            ? $address['type']
            : '';
    }
}
