<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\FastLaneCheckout\View\Blocks\Address;

use \XLite\Module\XC\FastLaneCheckout;

/**
 * Checkout Address form
 */
class Billing extends FastLaneCheckout\View\Blocks\Address
{
    /**
     * Returns block class name
     *
     * @return string
     */
    public function getAddressType()
    {
        return 'billing';
    }

    /**
     * @return void
     */
    protected function getEditAddressTitle()
    {
        return static::t('Edit billing address');
    }

    /**
     * Check - email field is visible or not
     *
     * @return boolean
     */
    protected function isEmailVisible()
    {
        return false;
    }

    /**
     * Get address info model
     *
     * @return \XLite\Model\Address
     */
    protected function getAddressInfo()
    {
        $profile = $this->getCart()->getProfile();

        return $profile ? $profile->getBillingAddress() : null;
    }

    /**
     * Check - shipping and billing addrsses are same or not
     *
     * @return boolean
     */
    protected function isSameAddress()
    {
        return is_null(\XLite\Core\Session::getInstance()->same_address)
            ? !$this->getCart()->getProfile() || $this->getCart()->getProfile()->isEqualAddress()
            : \XLite\Core\Session::getInstance()->same_address;
    }

    /**
     * Checks if same address mark can be displayed
     * 
     * @return boolean
     */
    protected function isSameAddressVisible()
    {
        return $this->isShippingNeeded();
    }
}
