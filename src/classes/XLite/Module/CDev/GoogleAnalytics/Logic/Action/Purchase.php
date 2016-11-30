<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\GoogleAnalytics\Logic\Action;

use XLite\Module\CDev\GoogleAnalytics;
use XLite\Module\CDev\GoogleAnalytics\Logic\DataMapper\OrderDataMapper;
use XLite\Module\CDev\GoogleAnalytics\Logic\DataMapper\OrderItemDataMapper;

class Purchase implements IAction
{
    /**
     * @return bool
     */
    public function isApplicable()
    {
        return \XLite\Module\CDev\GoogleAnalytics\Main::isECommerceEnabled()
            && \XLite::getController() instanceof \XLite\Controller\Customer\CheckoutSuccess
            && $this->getOrder()
            && GoogleAnalytics\Main::isPurchaseImmediatelyOnSuccess()
            && !$this->isOrderProcessed($this->getOrder());
    }

    /**
     * @return array
     */
    public function getActionData()
    {
        $result = [
            'ga-type'   => $this->getActionName(),
            'ga-action' => 'pageview',
            'data'      => $this->getPurchaseActionData($this->getOrder())
        ];

        $this->markOrderAsProcessed($this->getOrder());

        return $result;
    }

    /**
     * @return string
     */
    protected function getActionName()
    {
        return 'purchase';
    }

    /**
     * @param \XLite\Model\Order $order
     */
    protected function isOrderProcessed(\XLite\Model\Order $order)
    {
        $orders = \XLite\Core\Session::getInstance()->gaProcessedOrders;

        if (!is_array($orders)) {
            $orders = array();
        }

        return !$order
            || !$order->getProfile()
            || in_array($order->getOrderId(), $orders);
    }

    /**
     * @param \XLite\Model\Order $order
     */
    protected function markOrderAsProcessed(\XLite\Model\Order $order)
    {
        $orders = \XLite\Core\Session::getInstance()->gaProcessedOrders;

        if (!is_array($orders)) {
            $orders = array();
        }

        $orders[] = $order->getOrderId();

        \XLite\Core\Session::getInstance()->gaProcessedOrders = $orders;
    }

    /**
     * @param \XLite\Model\Order $order
     *
     * @return array
     */
    protected function getPurchaseActionData(\XLite\Model\Order $order)
    {
        $productsData = [];

        \XLite\Core\Translation::setTmpTranslationCode(\XLite\Core\Config::getInstance()->General->default_language);

        foreach ($order->getItems() as $item) {
            if (!$item->getObject()) {
                continue;
            }

            $productsData[] = OrderItemDataMapper::getData(
                $item,
                $item->getObject()->getCategory() ? $item->getObject()->getCategory()->getName() : ''
            );
        }

        \XLite\Core\Translation::setTmpTranslationCode(null);

        return [
            'products'      => $productsData,
            'actionData'    => OrderDataMapper::getPurchaseData($order),
        ];
    }

    /**
     * @return \XLite\Model\Order
     */
    protected function getOrder()
    {
        return method_exists(\XLite::getController(), 'getOrder')
            ? \XLite::getController()->getOrder()
            : null;
    }
}