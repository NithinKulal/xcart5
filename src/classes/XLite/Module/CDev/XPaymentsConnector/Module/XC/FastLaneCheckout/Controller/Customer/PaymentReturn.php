<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\XPaymentsConnector\Module\XC\FastLaneCheckout\Controller\Customer;

/**
 * Return to the store
 *
 * @Decorator\Depend({"CDev\XPaymentsConnector","XC\FastLaneCheckout"})

 */
class PaymentReturn extends \XLite\Controller\Customer\PaymentReturn implements \XLite\Base\IDecorator
{
    /**
     * Return
     *
     * @return void
     */
    protected function doActionReturn()
    {
        if (\XLite\Module\XC\FastLaneCheckout\Main::isFastlaneEnabled()) {

            $transaction = $this->detectXpcTransaction();
            if ($transaction) {
                if ($transaction->getOrder()->hasCartStatus()) {
                    // Set flag only if payment has been canceled and cart is not converted to order
                    \XLite\Core\Session::getInstance()->returnedAfterXpc = true;
                }
            }

        }

        parent::doActionReturn();
    }

}
