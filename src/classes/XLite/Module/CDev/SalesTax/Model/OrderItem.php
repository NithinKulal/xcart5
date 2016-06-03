<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\SalesTax\Model;

/**
 * OrderItem model extension
 */
class OrderItem extends \XLite\Model\OrderItem implements \XLite\Base\IDecorator
{
    /**
     * Shipping cost (part of order shipping cost distributed among order items)
     *
     * @var float
     */
    protected $shippingCost = 0;


    /**
     * Get order item shipping cost
     *
     * @return float
     */
    public function getShippingCost()
    {
        return $this->shippingCost;
    }

    /**
     * Set order item shipping cost
     *
     * @param float $value Value
     *
     * @return void
     */
    public function setShippingCost($value)
    {
        $this->shippingCost = $value;
    }
}
