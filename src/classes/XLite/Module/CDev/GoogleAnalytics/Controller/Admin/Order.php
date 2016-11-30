<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\GoogleAnalytics\Controller\Admin;

use XLite\Module\CDev\GoogleAnalytics\Logic\Action;
use XLite\Module\CDev\GoogleAnalytics\Logic\BackendActionExecutor;
use XLite\Module\CDev\GoogleAnalytics\Logic\DataMapper\OrderItemDataMapper;

class Order extends \XLite\Controller\Admin\Order implements \XLite\Base\IDecorator
{
    /**
     * @inheritdoc
     */
    protected function doActionUpdate()
    {
        if ($this->shouldRegisterChange()) {
            $old = $this->collectData($this->getOrder());

            parent::doActionUpdate();

            $new = $this->collectData($this->getOrder());

            $this->registerEvent(
                $this->getGAChanges($old, $new)
            );
        } else {
            parent::doActionUpdate();
        }
    }

    /**
     * @param \XLite\Model\Order $order
     *
     * @return array
     */
    protected function collectData(\XLite\Model\Order $order)
    {
        $tax      = $order->getSurchargeSumByType(\XLite\Model\Base\Surcharge::TYPE_TAX);
        $shipping = $order->getSurchargeSumByType(\XLite\Model\Base\Surcharge::TYPE_SHIPPING);

        return [
            'revenue'  => $order->getTotal(),
            'tax'      => $tax,
            'shipping' => $shipping,
            'items'    => $this->getItemsFingerprint($order->getItems()),
        ];
    }

    /**
     * @param $changes
     */
    protected function registerEvent($changes)
    {
        if (!array_filter($changes)) {
            return;
        }

        $changesToRegister = [
            'revenue'  => strval($changes['revenue']),
            'tax'      => strval($changes['tax']),
            'shipping' => strval($changes['shipping']),
        ];

        $refundItems   = [];
        $purchaseItems = [];
        $counter       = 1;
        foreach ($changes['items'] as $itemId => $itemData) {
            if ($itemData['change'] > 0) {
                $purchaseItems[] = OrderItemDataMapper::getDataForBackend($itemData['item'], $counter++, abs($itemData['change']));

            } elseif ($itemData['change'] < 0) {
                $refundItems[] = OrderItemDataMapper::getDataForBackend($itemData['item'], $counter++, abs($itemData['change']));
            }
        }

        if ($purchaseItems) {
            BackendActionExecutor::execute(
                new Action\PurchaseAdmin(
                    $this->getOrder(),
                    $purchaseItems
                )
            );
        }

        if ($refundItems) {
            BackendActionExecutor::execute(
                new Action\Refund(
                    $this->getOrder(),
                    $refundItems
                )
            );
        }

        $haveChangesToRegister = (bool) array_filter(array_values($changesToRegister));
        if ($haveChangesToRegister) {
            BackendActionExecutor::execute(
                new Action\TotalChange(
                    $this->getOrder(),
                    $changesToRegister
                )
            );
        }
    }

    /**
     * @param $old
     * @param $new
     *
     * @return array
     */
    protected function getGAChanges($old, $new)
    {
        $currency = $this->getOrder()->getCurrency();

        $changes = [];

        foreach ($new as $key => $newValue) {
            $oldValue = $old[$key];

            if ($key === 'items') {
                $changes[$key] = $this->getItemsChange($old[$key], $new[$key]);
            } else {
                $changes[$key] = $currency->roundValue($newValue - $oldValue);
            }
        }

        return array_combine(
            array_keys($new),
            array_values($changes)
        );
    }

    protected function getItemsFingerprint($items)
    {
        $result = [];
        foreach ($items as $item) {
            $result[$item->getItemId()] = [
                'item'   => $item,
                'amount' => $item->getAmount(),
            ];
        }

        return $result;
    }

    protected function getItemsChange($oldItems, $newItems)
    {
        $currency = $this->getOrder()->getCurrency();

        $allItems = [];
        foreach ($oldItems as $id => $item) {
            $allItems[$id] = $item['item'];
        }
        foreach ($newItems as $id => $item) {
            $allItems[$id] = $item['item'];
        }

        $changes = [];
        foreach ($allItems as $itemId => $item) {
            $oldAmount        = isset($oldItems[$itemId]['amount'])
                ? $oldItems[$itemId]['amount']
                : 0;
            $newAmount        = isset($newItems[$itemId]['amount'])
                ? $newItems[$itemId]['amount']
                : 0;
            $changes[$itemId] = [
                'item'   => $item,
                'change' => $currency->roundValue($newAmount - $oldAmount),
            ];
        }

        return $changes;
    }

    /**
     * @return bool
     */
    protected function shouldRegisterChange()
    {
        return \XLite\Module\CDev\GoogleAnalytics\Main::isECommerceEnabled()
        && !$this->getOrder()->isTemporary();
    }
}