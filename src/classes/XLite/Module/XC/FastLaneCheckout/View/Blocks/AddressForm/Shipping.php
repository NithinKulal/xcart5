<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\FastLaneCheckout\View\Blocks\AddressForm;

use \XLite\Module\XC\FastLaneCheckout;

/**
 * Checkout Address form
 */
class Shipping extends FastLaneCheckout\View\Blocks\AddressForm
{
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
        return false;
    }

    /**
     * Check - create profile checkbox is visible or not
     *
     * @return boolean
     */
    protected function isCreateVisible()
    {
        return false;
    }

    /**
     * @return string
     */
    public function getClassName()
    {
        return 'shipping';
    }

    /**
     * @return string
     */
    public function getShortAddressType()
    {
        return 's';
    }

    /**
     * Get JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = FastLaneCheckout\Main::getSkinDir() . 'blocks/address_form/shipping/address_form.js';

        return $list;
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
