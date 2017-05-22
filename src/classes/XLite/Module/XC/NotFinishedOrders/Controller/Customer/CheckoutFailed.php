<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\NotFinishedOrders\Controller\Customer;

/**
 * Checkout failed page controller
 */
class CheckoutFailed extends \XLite\Controller\Customer\CheckoutFailed implements \XLite\Base\IDecorator
{
    /**
     * Get failed cart object
     *
     * @return \XLite\Model\Cart
     */
    protected function getFailedCart()
    {
        return $this->getCart()->getNotFinishedOrder() ?: parent::getFailedCart();
    }
}
