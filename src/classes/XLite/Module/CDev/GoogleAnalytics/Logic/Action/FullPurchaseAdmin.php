<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\GoogleAnalytics\Logic\Action;

use XLite\Module\CDev\GoogleAnalytics\Logic\DataMapper\OrderDataMapper;
use XLite\Module\CDev\GoogleAnalytics\Logic\DataMapper\OrderItemDataMapper;

/**
 * Class FullPurchaseAdmin
 */
class FullPurchaseAdmin extends ABackendAction implements IBackendAction
{
    /**
     * @var \XLite\Model\Order
     */
    protected $order;

    /**
     * @param $order
     */
    public function __construct(\XLite\Model\Order $order)
    {
        $this->order = $order;
    }

    /**
     * @return bool
     */
    public function isBackendApplicable()
    {
        return \XLite\Module\CDev\GoogleAnalytics\Main::isECommerceEnabled()
            && $this->order;
    }

    /**
     * @return array
     */
    public function getActionDataForBackend()
    {
        $result = $this->getCommonDataForBackend();
        $result['ea'] = 'Purchase action';
        $result['pa'] = 'purchase';

        if ($this->order->getProfile()->getGaClientId()) {
            $result['cid']  = $this->order->getProfile()->getGaClientId();
        }

        $counter = 1;
        foreach ($this->order->getItems() as $item) {
            $result += $this->getProductData($item, $counter++);
        }

        $result += OrderDataMapper::getPurchaseDataForBackend($this->order);

        return $result;
    }

    /**
     * @param \XLite\Model\OrderItem $item
     *
     * @param                        $index
     *
     * @return array
     */
    protected function getProductData(\XLite\Model\OrderItem $item, $index)
    {
        if (!$item->getObject()) {
            return [];
        }

        return OrderItemDataMapper::getDataForBackend($item, $index);
    }
}