<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Customer;

/**
 * Shipping estimator
 */
class ShippingEstimate extends \XLite\Controller\Customer\ACustomer
{
    /**
     * Modifier (cache)
     *
     * @var \XLite\Model\Order\Modifier
     */
    protected $modifier;

    /**
     * Get page title
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Estimate shipping cost');
    }

    /**
     * Get address
     *
     * @return array
     */
    public function getAddress()
    {
        return \XLite\Model\Shipping::getInstance()->getDestinationAddress($this->getModifier()->getModifier());
    }

    /**
     * Get modifier
     *
     * @return \XLite\Model\Order\Modifier
     */
    public function getModifier()
    {
        if (!isset($this->modifier)) {
            $this->modifier = $this->getCart()->getModifier(\XLite\Model\Base\Surcharge::TYPE_SHIPPING, 'SHIPPING');
        }

        return $this->modifier;
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
    protected function doActionSetDestination()
    {
        $countryCode = \XLite\Core\Request::getInstance()->destination_country;

        $country = (!$countryCode && !$this->hasField('country_code'))
            ? \XLite\Model\Address::getDefaultFieldValue('country')
            : \XLite\Core\Database::getRepo('XLite\Model\Country')->find($countryCode);

        $state = null;

        if (0 < intval(\XLite\Core\Request::getInstance()->destination_state)) {
            $state = \XLite\Core\Database::getRepo('XLite\Model\State')
                ->find(\XLite\Core\Request::getInstance()->destination_state);

            if (isset($state)
                && $state->getCountry()->getCode() !== $countryCode
            ) {
                $state = null;
            }
        }

        if (!$state) {
            $state = \XLite\Core\Database::getRepo('XLite\Model\State')
                ->getOtherState(strval(\XLite\Core\Request::getInstance()->destination_custom_state));
        }

        if ($country
            && $country->getEnabled()
        ) {
            $address = $this->getCartProfile()->getShippingAddress();
            if (!$address) {
                $profile = $this->getCartProfile();
                $address = new \XLite\Model\Address;
                $address->setProfile($profile);
                $address->setIsShipping(true);
                $address->setIsBilling(true);
                $address->setIsWork(true);
                $profile->addAddresses($address);
                \XLite\Core\Database::getEM()->persist($address);
            }

            $address->setCountry($country);

            if ($state) {
                if (0 < $state->getStateId()) {
                    $address->setState($state);

                } else {
                    $address->setState(null);
                    $address->setCustomState($state->getState());
                }
            }

            $address->setZipcode(\XLite\Core\Request::getInstance()->destination_zipcode);

            $address->setType(
                \XLite\Core\Request::getInstance()->destination_type
                    ?: \XLite\Core\Config::getInstance()->Shipping->anonymous_address_type
            );

            $address->update();

            // Update cart and do not call js-event 'updateCart' to prevent race of shipping rates calculation
            $this->updateCart(true);

            $modifier = $this->getCart()->getModifier('shipping', 'SHIPPING');

            if ($modifier) {
                $shippingAddress = \XLite\Model\Shipping::getInstance()
                    ->getDestinationAddress($modifier->getModifier());
            }

            $this->valid = true;

            $this->setInternalRedirect();

        } else {
            \XLite\Core\TopMessage::addError('Shipping address is invalid');

            $this->valid = false;
        }
    }

    /**
     * Change shipping method
     * @todo: refactor (decompose)
     *
     * @return void
     */
    protected function doActionChangeMethod()
    {
        $methodId = \XLite\Core\Request::getInstance()->methodId;
        $cart = $this->getCart();

        if (null !== $methodId
            && $cart->getShippingId() != $methodId
        ) {
            $cart->setLastShippingId($methodId);
            $cart->setShippingId($methodId);

            $address = $this->getCartProfile()->getShippingAddress();
            if (!$address) {
                // Default address
                $profile = $this->getCartProfile();
                $address = new \XLite\Model\Address;

                $addr = $this->getAddress();

                // Country
                $c = 'US';

                if ($addr && isset($addr['country'])) {
                    $c = $addr['country'];
                    $country = \XLite\Core\Database::getRepo('XLite\Model\Country')->find($c);

                } elseif (\XLite\Model\Address::getDefaultFieldValue('country')) {
                    $country = \XLite\Model\Address::getDefaultFieldValue('country');
                } else {
                    $country = \XLite\Core\Database::getRepo('XLite\Model\Country')->find($c);
                }

                if ($country) {
                    $address->setCountry($country);
                }

                // State
                $state = null;

                if ($addr && !empty($addr['state'])) {
                    $state = \XLite\Core\Database::getRepo('XLite\Model\State')->find($addr['state']);
                } elseif (
                    !$addr
                    && \XLite\Model\Address::getDefaultFieldValue('state')
                ) {
                    $state = \XLite\Model\Address::getDefaultFieldValue('state');
                }

                if ($state) {
                    $address->setState($state);
                }

                // Zip code
                if (\XLite\Model\Address::getDefaultFieldValue('zipcode')) {
                    $address->setZipcode(\XLite\Model\Address::getDefaultFieldValue('zipcode'));
                }

                $address->setProfile($profile);
                $address->setIsShipping(true);
                $profile->addAddresses($address);
                \XLite\Core\Database::getEM()->persist($address);
            }

            $this->updateCart();

        }

        $this->valid = true;
        $this->setSilenceClose();
    }
}
