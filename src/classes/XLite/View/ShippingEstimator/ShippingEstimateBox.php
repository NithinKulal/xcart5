<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\ShippingEstimator;

/**
 * Shipping estimate box
 *
 * @ListChild (list="cart.panel.box", weight="10")
 */
class ShippingEstimateBox extends \XLite\View\AView
{
    /**
     * Modifier (cache)
     *
     * @var \XLite\Model\Order\Modifier
     */
    protected $modifier;

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'form_field/select_country.js';
        $list[] = 'shopping_cart/parts/box.estimator.js';

        return $list;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'shopping_cart/parts/box.estimator.twig';
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && $this->getModifier()
            && $this->getModifier()->canApply()
            && $this->isAddressFieldsEnabled();
    }

    /**
     * Check if view should reload ajax-ly after page load (in case of online shippings)
     *
     * @return boolean
     */
    public function shouldDeferLoad()
    {
        return \XLite\Model\Shipping::getInstance()->hasOnlineProcessors();
    }

    /**
     * Check - shipping estimate and method selected or not
     *
     * @return boolean
     */
    protected function isShippingEstimate()
    {
        return \XLite\Model\Shipping::getInstance()->getDestinationAddress($this->getModifier()->getModifier())
            && $this->getModifier()->getMethod();
    }

    /**
     * Get modifier
     *
     * @return \XLite\Model\Order\Modifier
     */
    protected function getModifier()
    {
        if (!isset($this->modifier)) {
            $this->modifier = $this->getCart()->getModifier(\XLite\Model\Base\Surcharge::TYPE_SHIPPING, 'SHIPPING');
        }

        return $this->modifier;
    }

    /**
     * Return true if all address fields ar enabled
     *
     * @return boolean
     */
    protected function isAddressFieldsEnabled()
    {
        $result = false;

        $allowedAddressFields = array(
            'country_code',
            'state_id',
            'custom_state',
            'zipcode',
        );

        // Find all enabled address fields
        $enabledAddressFields = \XLite\Core\Database::getRepo('XLite\Model\AddressField')->findByEnabled(true);

        if ($enabledAddressFields) {
            $addressFields = array();
            foreach ($enabledAddressFields as $field) {
                $addressFields[] = $field->getServiceName();
            }

            $addressFields = array_intersect($addressFields, $allowedAddressFields);

            if ($addressFields) {
                // Get processors required address fields
                $processorFields = \XLite\Model\Shipping::getRequiredAddressFields();

                if ($processorFields) {
                    foreach ($processorFields as $processor => $fields) {
                        $intersect = array_intersect($fields, $addressFields);

                        if (count($intersect) === count($fields)) {
                            $result = true;
                            break;
                        }
                    }
                }
            }
        }

        return $result;
    }

    /**
     * Get shipping estimate address
     *
     * @return string
     */
    protected function getEstimateAddress()
    {
        $string = '';

        $address = \XLite\Model\Shipping::getInstance()->getDestinationAddress($this->getModifier()->getModifier());
        $state = null;

        if (is_array($address)) {
            $country = \XLite\Core\Database::getRepo('XLite\Model\Country')->find($address['country']);

            if (!empty($address['state'])) {
                if (is_integer($address['state'])) {
                    $state = \XLite\Core\Database::getRepo('XLite\Model\State')->find($address['state']);

                } elseif (!empty($address['country'])) {
                    $state = \XLite\Core\Database::getRepo('XLite\Model\State')->findOneByCountryAndCode($address['country'], $address['state']);
                }

            } elseif ($this->getCart()->getProfile() && $this->getCart()->getProfile()->getShippingAddress()) {
                $state = $this->getCart()->getProfile()->getShippingAddress()->getState();
            }

            if ($state
                && $country
                && (!$state->getCountry()
                    || $state->getCountry()->getCode() != $country->getCode()
                )
            ) {
                $state = \XLite\Core\Database::getRepo('XLite\Model\State')->getOtherState($address['custom_state']);
            }
        }

        if (isset($country)) {
            $string = $country->getCountry();
        }

        if ($state && $state->getState()) {
            $string .= ', ' . ($state->getCode() && 3 > strlen($state->getCode()) ? $state->getCode() : $state->getState());
        }

        $string .= ', ' . $address['zipcode'];

        $string = rtrim(rtrim($string), ',');

        return $string;
    }
}
