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
class Billing extends FastLaneCheckout\View\Blocks\AddressForm
{
    /**
     * @return string
     */
    public function getClassName()
    {
        return 'billing';
    }

    /**
     * @return string
     */
    public function getShortAddressType()
    {
        return 'b';
    }

    /**
     * Modifier (cache)
     *
     * @var \XLite\Model\Order\Modifier
     */
    protected $modifier;

    /**
     * Get JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = FastLaneCheckout\Main::getSkinDir() . 'blocks/address_form/billing/address_form.js';

        return $list;
    }

    /**
     * Check - email field is visible or not
     *
     * @return boolean
     */
    protected function isEmailVisible()
    {
        return !$this->getModifier() || !$this->getModifier()->canApply();
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
     * Check - form is visible or not
     *
     * @return boolean
     */
    protected function isFormVisible()
    {
        return !$this->isSameAddress() || !$this->isSameAddressVisible();
    }

    /**
     * Check - shipping and billing addrsses are same or not
     *
     * @return boolean
     */
    protected function isSameAddress()
    {
        return is_null(\XLite\Core\Session::getInstance()->same_address)
            ? $this->isSameAddressVisible() && (!$this->getCart()->getProfile() || $this->getCart()->getProfile()->isEqualAddress())
            : \XLite\Core\Session::getInstance()->same_address && $this->getCart()->isShippingSectionVisible();
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
            : ('billingAddress[' . $name . ']');
    }

    /**
     * Check - same address box is visible or not
     *
     * @return boolean
     */
    protected function isSameAddressVisible()
    {
        return $this->getModifier() && $this->getModifier()->canApply();
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
     * Get address info model
     *
     * @return \XLite\Model\Address
     */
    protected function getAddressInfo()
    {
        $profile = $this->getCart()->getProfile();

        return ($this->isFormVisible() && $profile)
            ? $profile->getBillingAddress()
            : null;
    }

    /**
     * Check - display save new field or not
     *
     * @return boolean
     */
    protected function isSaveNewField()
    {
        return parent::isSaveNewField()
            && $this->isFormVisible();
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
        $data[\XLite\View\FormField\Select\Country::PARAM_STATE_SELECTOR_ID] = 'billingaddress-state-id';
        $data[\XLite\View\FormField\Select\Country::PARAM_STATE_INPUT_ID]    = 'billingaddress-custom-state';

        return $data;
    }
}
