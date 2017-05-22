<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\NotFinishedOrders\Module\CDev\Paypal\Controller\Customer;

/**
 * Class Checkout
 *
 * @Decorator\Depend ("CDev\Paypal")
 * @Decorator\After ("XC\NotFinishedOrders")
 */
class Checkout extends \XLite\Controller\Customer\Checkout implements \XLite\Base\IDecorator
{
    /**
     * Return true if specified processor allows to create NFO on place order action
     *
     * @return boolean
     */
    protected function isAllowedPlaceOrderNFO()
    {
        $cart = $this->getCart();
        $method = $cart->getPaymentMethod();

        return parent::isAllowedPlaceOrderNFO()
            && $method
            && !$cart->isExpressCheckout($method);
    }

}
