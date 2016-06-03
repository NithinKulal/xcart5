<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductVariants\Core;

/**
 * XPayments client
 *
 */
class OrderHistory extends \XLite\Core\OrderHistory implements \XLite\Base\IDecorator
{
    /**
     * Register the change amount inventory
     *
     * @param integer              $orderId Order identifier
     * @param \XLite\Model\Product $product Product object
     * @param integer              $delta   Inventory delta changes
     *
     * @return void
     */
    public function registerChangeAmount($orderId, $product, $delta)
    {
        if (!$product->hasVariants()) {
            parent::registerChangeAmount($orderId, $product, $delta);
        }
    }

    /**
     * Register the change amount inventory
     *
     * @param integer                                               $orderId Order identifier
     * @param \XLite\Module\XC\ProductVariants\Model\ProductVariant $variant Product variant object
     * @param integer                                               $delta   Inventory delta changes
     *
     * @return void
     */
    public function registerChangeVariantAmount($orderId, $variant, $delta)
    {
        /** @var \XLite\Model\Product $product */
        $product = $variant->getProduct();

        if (!$variant->getDefaultAmount() || $product->getInventoryEnabled()) {
            $this->registerEvent(
                $orderId,
                static::CODE_CHANGE_AMOUNT,
                $this->getOrderChangeAmountDescription($orderId, $delta, $product),
                $this->getOrderChangeAmountData($orderId, $product->getName(), $variant->getPublicAmount() - $delta, $delta)
            );
        }
    }
}
