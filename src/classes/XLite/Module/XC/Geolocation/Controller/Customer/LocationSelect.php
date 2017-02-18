<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Geolocation\Controller\Customer;

/**
 * Shipping estimator
 */
class LocationSelect extends \XLite\Controller\Customer\ACustomer
{
    /**
     * Get page title
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Choose your location');
    }

    /**
     * Get address
     *
     * @return array
     */
    public function getAddress()
    {
        $address = $this->getCart() && $this->getCart()->getProfile() && $this->getCart()->getProfile()->getShippingAddress()
                    ? $this->getCart()->getProfile()->getShippingAddress()
                    : null;

        return $address ? $address->toArray() : \XLite\Model\Shipping::getDefaultAddress();
    }

    /**
     * Check if the enabled address field with the given name exists
     *
     * @param string $fieldName Field name
     *
     * @return boolean
     */
    public function hasField($fieldName)
    {
        return (bool) \XLite\Core\Database::getRepo('XLite\Model\AddressField')->findOneBy(
            array(
                'serviceName' => $fieldName,
                'enabled'     => true,
            )
        );
    }

    /**
     * Common method to determine current location
     *
     * @return string
     */
    protected function getLocation()
    {
        return $this->getTitle();
    }

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

        $state = null;

        if (0 < intval(\XLite\Core\Request::getInstance()->address_state)) {
            $state = \XLite\Core\Database::getRepo('XLite\Model\State')
                ->find(\XLite\Core\Request::getInstance()->address_state);

            if (
                isset($state)
                && $state->getCountry()->getCode() != $countryCode
            ) {
                $state = null;
            }
        }

        if (!$state) {
            $state = \XLite\Core\Database::getRepo('XLite\Model\State')
                ->getOtherState(strval(\XLite\Core\Request::getInstance()->address_custom_state));
        }

        if (
            $country
            && $country->getEnabled()
        ) {
            $address = $this->getCart() && $this->getCart()->getProfile() && $this->getCart()->getProfile()->getShippingAddress()
                    ? $this->getCart()->getProfile()->getShippingAddress()
                    : null;
            if (!$address) {
                $location = array(
                    'country' => $country->getCode(),
                    'zipcode' => \XLite\Core\Request::getInstance()->address_zipcode,
                );

                if ($state) {
                    $location['state'] = $state->getStateId() > 0 ? $state->getCode() : $state->getState();
                }

                \XLite\Module\XC\Geolocation\Logic\Geolocation::getInstance()->setCachedLocation($location);

            } else {
                $address->setCountry($country);

                if ($state) {
                    if ($state->getStateId() > 0) {
                        $address->setState($state);

                    } else {
                        $address->setState(null);
                        $address->setCustomState($state->getState());
                    }
                }

                $address->setZipcode(\XLite\Core\Request::getInstance()->address_zipcode);

                $address->setType(
                    \XLite\Core\Request::getInstance()->address_type ?: \XLite\Core\Config::getInstance()->Shipping->anonymous_address_type
                );

                $address->update();

                // Update cart and do not call js-event 'updateCart' to prevent race of shipping rates calculation
                $this->updateCart(true);
            }

            \XLite\Core\TopMessage::addInfo('Location was successfully set');

            $this->valid = true;

            header('Location-data: ' . $country->getCountry());

        } else {
            \XLite\Core\TopMessage::addError('Location is invalid');

            $this->valid = false;
        }
    }
}
