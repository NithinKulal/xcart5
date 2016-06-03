<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\View;

use \XLite\Module\CDev\Paypal\Model\Payment\Processor\ExpressCheckout as ExpressCheckoutProcessor;

/**
 * Extend Checkout\Step\Review widget
 */
class CheckoutReview extends \XLite\View\Checkout\Step\Review implements \XLite\Base\IDecorator
{
    /**
     * Return false if Express Checkout shortcut is selected by customer
     * 
     * @return boolean
     */
    protected function isNeedReplaceLabel()
    {
        $result = parent::isNeedReplaceLabel();

        if ($result) {
            $cart = $this->getCart();

            if (
                $cart->isExpressCheckout($cart->getPaymentMethod())
                && ExpressCheckoutProcessor::EC_TYPE_SHORTCUT == \XLite\Core\Session::getInstance()->ec_type
            ) {
                $result = false;
            }
        }

        return $result;
    }
}
