<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Logic\Order\Modifier;

/**
 * Abstract Discount modifier - for discounts which should be aggregated
 * and displayed as a single 'Discount' line in cart/order totals
 */
abstract class Discount extends \XLite\Logic\Order\Modifier\ADiscount
{
    /**
     * Modifier unique code
     *
     * @var string
     */
    protected $code = 'DISCOUNT';

    // {{{ Surcharge operations

    /**
     * Get surcharge name
     *
     * @param \XLite\Model\Base\Surcharge $surcharge Surcharge
     *
     * @return \XLite\DataSet\Transport\Order\Surcharge
     */
    public function getSurchargeInfo(\XLite\Model\Base\Surcharge $surcharge)
    {
        $info = new \XLite\DataSet\Transport\Order\Surcharge;
        $info->name = \XLite\Core\Translation::lbl('Discount');

        return $info;
    }

    // }}}

    /**
     * Distribute discount among the ordered products
     *
     * @param float  $discountTotal Discount value
     *
     * @return void
     */
    public function distributeDiscount($discountTotal)
    {
        // Get order items
        $orderItems = $this->getOrderItems();

        $this->distributeDiscountAmongItems($discountTotal, $orderItems);
    }

    /**
     * Distribute discount among the passed ordered products
     *
     * @param float  $discountTotal Discount value
     * @param array  $orderItems    Items for distribution
     *
     * @return void
     */
    public function distributeDiscountAmongItems($discountTotal, $orderItems)
    {
        // Order currency
        $currency = $this->getOrder()->getCurrency();

        // Initialize service variables
        $subtotal = 0;
        $distributedSum = 0;
        $lastItemKey = null;

        // Calculate sum of subtotals of all items
        foreach ($orderItems as $key => $item) {
            $subtotal += $item->getSubtotal();
        }

        foreach ($orderItems as $key => $item) {
            // Calculate item discount value
            $discountValue = is_numeric($subtotal) && $subtotal > 0
                ? $currency->roundValue(($item->getSubtotal() / $subtotal) * $discountTotal)
                : 0;

            // Set discounted subtotal for item
            $item->setDiscountedSubtotal($item->getDiscountedSubtotal() - $discountValue);

            // Update distributed discount value
            $distributedSum += $discountValue;

            // Remember last used item
            $lastItemKey = $key;
        }

        if ($distributedSum != $discountTotal) {
            // Correct last item's discount
            $orderItems[$lastItemKey]->setDiscountedSubtotal(
                $orderItems[$lastItemKey]->getDiscountedSubtotal() + $discountTotal - $distributedSum
            );
        }
    }

    /**
     * Returns order items
     *
     * @return array
     */
    protected function getOrderItems()
    {
        return $this->getOrder()->getItems();
    }
}
