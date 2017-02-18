<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\XPaymentsConnector\Module\XC\FastLaneCheckout\Controller\Customer;

/**
 * Checkout 
 *
 * @Decorator\Depend({"CDev\XPaymentsConnector","XC\FastLaneCheckout"})
 */
class Checkout extends \XLite\Controller\Customer\Checkout implements \XLite\Base\IDecorator
{
    /**
     * Check if customer is returned from X-Payments page
     *
     * @return boolean
     */
    public function isReturnedAfterXpc()
    {
        $result = \XLite\Core\Session::getInstance()->returnedAfterXpc;

        \XLite\Core\Session::getInstance()->returnedAfterXpc = null;

        return $result;
    }

    /**
     * Clear init data from session and redirrcet back to checkout
     *
     * @return void
     */
    protected function doActionClearInitData()
    {
        if (\XLite\Module\XC\FastLaneCheckout\Main::isFastlaneEnabled()) {
            \XLite\Core\Session::getInstance()->returnedAfterXpc = true;
        }

        parent::doActionClearInitData();
    }


}
