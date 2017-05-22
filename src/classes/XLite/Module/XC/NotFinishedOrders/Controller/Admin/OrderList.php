<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\NotFinishedOrders\Controller\Admin;

/**
 * Order list controller
 */
class OrderList extends \XLite\Controller\Admin\OrderList implements \XLite\Base\IDecorator
{
    /**
     * doActionUpdate
     *
     * @return void
     */
    protected function doActionUpdateItemsList()
    {
        $changes = $this->getOrdersChanges();

        foreach ($changes as $orderId => $change) {
            if (!empty($change['paymentStatus']) || !empty($change['shippingStatus'])) {
                $order = \XLite\Core\Database::getRepo('XLite\Model\Order')->find($orderId);

                if ($order && $order->isNotFinishedOrder()) {
                    $order->closeNotFinishedOrder();
                }
            }
        }

        parent::doActionUpdateItemsList();
    }
}
