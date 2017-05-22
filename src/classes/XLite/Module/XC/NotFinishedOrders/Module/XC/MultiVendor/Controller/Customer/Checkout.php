<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\NotFinishedOrders\Module\XC\MultiVendor\Controller\Customer;

/**
 * Checkout controller extension
 *
 * @Decorator\Depend ("XC\MultiVendor")
 */
class Checkout extends \XLite\Controller\Customer\Checkout implements \XLite\Base\IDecorator
{
    /**
     * Return true if profile can be cloned
     *
     * @param \XLite\Model\Order $order Order model object
     *
     * @return boolean
     */
    protected function isAllowedCloneProfile($order)
    {
        return parent::isAllowedCloneProfile($order)
            && !($order && $order->isNotFinishedOrder());
    }
}
