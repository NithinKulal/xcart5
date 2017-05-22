<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\NotFinishedOrders\View\ItemsList\Model\Payment;

/**
 * Payment transactions items list
 *
 * @Decorator\Depend ("XC\MultiVendor")
 */
class MultiVendorTransaction extends \XLite\View\ItemsList\Model\Payment\Transaction implements \XLite\Base\IDecorator
{
    /**
     * Return orders for 'order' column
     *
     * @param \XLite\Model\Payment\Transaction $entity Entity
     *
     * @return \XLite\Model\Order[]
     */
    protected function getOrders($entity)
    {
        /** @var \XLite\Model\Order $order */
        $order = $entity->getOrder();

        return $order->isNotFinishedOrder()
            ? array($order)
            : parent::getOrders($entity);
    }
}
