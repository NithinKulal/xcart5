<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\NotFinishedOrders\Controller\Admin;

/**
 * Class represents an order
 */
class Order extends \XLite\Controller\Admin\Order implements \XLite\Base\IDecorator
{
    /**
     * Return true if order can be edited
     *
     * @return boolean
     */
    public function isOrderEditable()
    {
        return parent::isOrderEditable() && !($this->getOrder() instanceof \XLite\Model\Cart);
    }

    /**
     * Get order
     *
     * @return \XLite\Model\Order
     */
    public function getOrder()
    {
        $order = parent::getOrder();

        if (null === $order && \XLite\Core\Request::getInstance()->order_id) {
            $order = \XLite\Core\Database::getRepo('XLite\Model\Cart')
                ->find((int) \XLite\Core\Request::getInstance()->order_id);

            $this->order = $order && $order->isNotFinishedOrder()
                ? $order
                : null;
        }

        return $this->order;
    }

    /**
     * doActionUpdate
     *
     * @return void
     */
    protected function doActionUpdate()
    {
        $order = $this->getOrder();

        if ($order->isNotFinishedOrder()) {
            $order->closeNotFinishedOrder();
        }

        parent::doActionUpdate();
    }
}