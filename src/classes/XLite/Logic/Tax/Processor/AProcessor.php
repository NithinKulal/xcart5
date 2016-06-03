<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Logic\Tax\Processor;

/**
 * Abstract tax processor
 */
abstract class AProcessor extends \XLite\Logic\ALogic
{
    /**
     * Order
     *
     * @var \XLite\Model\Order
     */
    protected $order;

    /**
     * Set processor context
     *
     * @param \XLite\Model\Order $order Context
     *
     * @return void
     */
    public function setContext(\XLite\Model\Order $order)
    {
        $this->order = $order;
    }

    /**
     * Get processor context
     *
     * @return \XLite\Model\Order
     */
    public function getContext()
    {
        return $this->order;
    }

    /**
     * Check if process is ready or not
     *
     * @return boolean
     */
    protected function isReady()
    {
        return (bool) $this->order;
    }

    // {{{ Catalog displayed price calculation

    /**
     * Check - processor is modify product price or not
     *
     * @return boolean
     */
    public function isProductPriceModifier()
    {
        return false;
    }

    /**
     * Reverse product price
     *
     * @param \XLite\Model\Product $product Product
     * @param float                $amount  Currenct product price OPTIONAL
     *
     * @return float
     */
    public function reverseProductPrice(\XLite\Model\Product $product, $amount = null)
    {
        return $amount ?: $product->getPrice();
    }

    /**
     * Restore product price
     *
     * @param \XLite\Model\Product $product Product
     * @param float                $amount  Product restored price
     *
     * @return float
     */
    public function restoreProductPrice(\XLite\Model\Product $product, $amount)
    {
        return $amount;
    }

    // }}}

    // {{{ Order calculate

    /**
     * Calculate order tax
     *
     * @return void
     */
    public function calculateOrderTax()
    {
    }

    // }}}
}
