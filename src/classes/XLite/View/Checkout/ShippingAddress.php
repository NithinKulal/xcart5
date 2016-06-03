<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Checkout;

/**
 * Shipping address block
 */
class ShippingAddress extends \XLite\View\Checkout\AAddressBlock
{
    /**
     * Get JS files 
     * 
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = 'checkout/steps/shipping/parts/address.shipping.js';

        return $list;
    }

    /**
     * Check - email field is visible or not
     *
     * @return boolean
     */
    protected function isEmailVisible()
    {
        return true;
    }

    /**
     * Check - password field is visible or not
     *
     * @return boolean
     */
    protected function isPasswordVisible()
    {
        return $this->isAnonymous();
    }

    /**
     * Get field name 
     * 
     * @param string $name File short name
     *  
     * @return string
     */
    protected function getFieldFullName($name)
    {
        return in_array($name, array('email', 'password'))
            ? $name
            : ('shippingAddress[' . $name . ']');
    }

    /**
     * Get address info model
     *
     * @return \XLite\Model\Address
     */
    protected function getAddressInfo()
    {
        $profile = $this->getCart()->getProfile();

        return $profile ? $profile->getShippingAddress() : null;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'checkout/steps/shipping/parts/shippingAddress.twig';
    }

    /**
     * Check - 'Save as new' checkbox checked or not
     * 
     * @return boolean
     */
    protected function isSaveAsNewChecked()
    {
        return $this->getAddressInfo() && !$this->getAddressInfo()->getIsWork();
    }

    /**
     * Add some data for country_code field
     *
     * @param array $data Array of field data
     *
     * @return array
     */
    protected function prepareFieldParamsCountryCode($data)
    {
        $data[\XLite\View\FormField\Select\Country::PARAM_STATE_SELECTOR_ID] = 'shippingaddress-state-id';
        $data[\XLite\View\FormField\Select\Country::PARAM_STATE_INPUT_ID]    = 'shippingaddress-custom-state';

        return $data;
    }
}
