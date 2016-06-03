<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MultiCurrency\XDependencies\View;

use XLite\Module\XC\MultiCurrency\Core\MultiCurrency;

/**
 * TODO: Resolve the situation that calls for this workaround by using formatInvoicePrice() in MultiVendor module
 * TODO:   or by providing ability to override these functions without the need to copy the code from MultiVendor
 *
 * Invoice widget
 *
 * @Decorator\Depend({"XC\MultiVendor","XC\MultiCurrency"})
 */
class Invoice extends \XLite\View\Invoice implements \XLite\Base\IDecorator
{
    /**
     * Get order formatted subtotal
     *
     * @return string
     */
    protected function getOrderTotal()
    {
        return $this->formatInvoicePrice($this->getOrderTotalForVendor(), $this->getOrder()->getCurrency(), false);
    }

    /**
     * Get order formatted subtotal
     *
     * @return string
     */
    protected function getOrderSubtotal()
    {
        return $this->formatInvoicePrice($this->getOrderSubtotalForVendor(), $this->getOrder()->getCurrency(), false);
    }

    /**
     * Get order total for current vendor
     *
     * @return float
     */
    protected function getOrderTotalForVendor()
    {
        $order = $this->getOrder();
        $result = $order->getTotal();
        $vendor = $this->getVendor();

        if ($vendor) {
            if ($order->isSingle()) {
                $itemsResult = array_reduce(
                    $order->getItems()->toArray(),
                    function ($carry, $item) use ($vendor) {
                        $ofCurrentVendor = $item->getVendor()
                            && $item->getVendor()->getProfileId() === $vendor->getProfileId();

                        return $carry + ($ofCurrentVendor ? $item->getTotal() : 0);
                    },
                    0
                );

                $surchargesResult = array_reduce(
                    $order->getSurcharges()->toArray(),
                    function ($carry, $item) use ($vendor) {
                        $ofCurrentVendor = $item->getVendor()
                            && $item->getVendor()->getProfileId() === $vendor->getProfileId();

                        return $carry + ($ofCurrentVendor ? $item->getValue() : 0);
                    },
                    0
                );

                $result = $itemsResult + $surchargesResult;

            } elseif (!$order->getVendor() || $order->getVendor()->getProfileId() !== $vendor->getProfileId()) {
                $result = $order->getChildByVendor($vendor)->getTotal();
            }
        }

        return $result;
    }

    /**
     * Get order subtotal for current vendor
     *
     * @return float
     */
    protected function getOrderSubtotalForVendor()
    {
        $order = $this->getOrder();
        $result = $order->getSubtotal();
        $vendor = $this->getVendor();

        if ($vendor) {
            if ($order->isSingle()) {
                $result = array_reduce($order->getItems()->toArray(), function ($carry, $item) use ($vendor) {
                    $ofCurrentVendor = $item->getVendor()
                        && $item->getVendor()->getProfileId() === $vendor->getProfileId();

                    return $carry + ($ofCurrentVendor ? $item->getTotal() : 0);
                }, 0);

            } elseif (!$order->getVendor() || $order->getVendor()->getProfileId() !== $vendor->getProfileId()) {
                $result = $order->getChildByVendor($vendor)->getSubtotal();
            }
        }

        return $result;
    }
}
