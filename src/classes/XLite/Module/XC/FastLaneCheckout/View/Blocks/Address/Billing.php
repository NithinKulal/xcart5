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
}
