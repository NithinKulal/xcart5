<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\NotFinishedOrders\Core;

/**
 * OrderHistory
 */
class OrderHistory extends \XLite\Core\OrderHistory implements \XLite\Base\IDecorator
{
    /**
     * Register "Place order" event to the order history
     *
     * @param integer $orderId Order id
     *
     * @return void
     */
    public function registerPlaceOrder($orderId)
    {
        $order = \XLite\Core\Database::getRepo('XLite\Model\Order')->find($orderId);

        if ($order && !$order->getNotFinishedOrder()) {
            // Do not register 'place order' event for not finished orders
            parent::registerPlaceOrder($orderId);
        }
    }
}
