<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\NotFinishedOrders\Module\CDev\Paypal\Model\Payment\Processor;

use XLite\Module\XC\NotFinishedOrders\Main;

/**
 * @Decorator\Depend ("CDev\Paypal")
 */
class ExpressCheckoutMerchantAPI extends \XLite\Module\CDev\Paypal\Model\Payment\Processor\ExpressCheckoutMerchantAPI implements \XLite\Base\IDecorator
{
    public function doSetExpressCheckout(\XLite\Model\Payment\Method $method)
    {
        $result = parent::doSetExpressCheckout($method);

        if (Main::isCreateOnPlaceOrder()) {
            $cart = \XLite\Model\Cart::getInstance();
            $cart->processNotFinishedOrder(true);
        }

        return $result;
    }
}
