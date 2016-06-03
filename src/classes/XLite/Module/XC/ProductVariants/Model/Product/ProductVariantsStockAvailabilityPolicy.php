<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */


namespace XLite\Module\XC\ProductVariants\Model\Product;

use Includes\Utils\ArrayManager;
use XLite\Model\Cart;
use XLite\Model\Product;
use XLite\Module\XC\ProductVariants\Model\ProductVariant;

class ProductVariantsStockAvailabilityPolicy extends \XLite\Model\Product\ProductStockAvailabilityPolicy implements \XLite\Base\IDecorator
{
    const PRODUCT_HAS_VARIANTS        = 'product_has_variants';
    const PRODUCT_VARIANTS            = 'product_variants';
    const VARIANT_USE_PRODUCTS_AMOUNT = 'variant_use_products_amount';
    const VARIANT_ID                  = 'variant_id';
    const VARIANT_AMOUNT              = 'variant_amount';

    /**
     * Check if product is out of stock
     *
     * @param Cart $cart
     *
     * @return bool
     */
    public function isOutOfStock(Cart $cart)
    {
        if (!$this->dto[self::PRODUCT_HAS_VARIANTS]) {
            return parent::isOutOfStock($cart);
        } else {

            foreach ($this->dto[self::PRODUCT_VARIANTS] as $v) {
                if ($v[self::VARIANT_USE_PRODUCTS_AMOUNT]) {
                    if (!parent::isOutOfStock($cart)) {
                        return false;
                    }

                } else if (!$this->isVariantOutOfStock($cart, $v[self::VARIANT_ID])) {
                    return false;
                }
            }

            return true;
        }
    }

    // TODO: Try to extract getAvailableVariantAmount() & canAddVariantToCart() into a separate ProductVariantStockAvailabilityPolicy similar to ProductStockAvailabilityPolicy. This seems especially reasonable together with converting ProductStockAvailabilityPolicy to the true value object (see doc block comment for the latter).

    /**
     * Get available amount for a specific variant
     *
     * @param Cart $cart
     * @param      $variantId
     *
     * @return int
     */
    public function getAvailableVariantAmount(Cart $cart, $variantId)
    {
        $variant = $this->dto[self::PRODUCT_VARIANTS][$variantId];

        if ($variant[self::VARIANT_USE_PRODUCTS_AMOUNT]) {
            return parent::getAvailableAmount($cart);
        } else {
            $cartItems  = $cart->getItemsByVariantId($variant[self::VARIANT_ID]);
            $cartAmount = ArrayManager::sumObjectsArrayFieldValues($cartItems, 'getAmount', true);

            return max(0, $variant[self::VARIANT_AMOUNT] - $cartAmount);
        }
    }

    /**
     * Check if specific variant is out of stock
     *
     * @param Cart $cart
     * @param      $variantId
     *
     * @return bool
     */
    public function isVariantOutOfStock(Cart $cart, $variantId)
    {
        return $this->getAvailableVariantAmount($cart, $variantId) <= 0;
    }

    /**
     * Get first variant that is available for adding to cart
     *
     * @param Cart $cart
     *
     * @return bool|null
     */
    public function getFirstAvailableVariantId(Cart $cart)
    {
        foreach ($this->dto[self::PRODUCT_VARIANTS] as $variantId => $_) {
            if (!$this->isVariantOutOfStock($cart, $variantId)) {
                return $variantId;
            }
        }

        return null;
    }

    /**
     * Create a data transfer object out of the Product instance.
     * This object should contain all of the data necessary for getAvailableAmount() & canAddToCart() methods to compute their value.
     *
     * @param Product $product
     *
     * @return array
     */
    protected function createDTO(Product $product)
    {
        $variants = array_map(function ($v) {
            /** @var ProductVariant $v */
            return [
                self::VARIANT_ID                  => $v->getId(),
                self::VARIANT_USE_PRODUCTS_AMOUNT => $v->getDefaultAmount(),
                self::VARIANT_AMOUNT              => $v->getAmount(),
            ];
        }, $product->getVariants()->toArray());

        $variantIds = array_map(function ($v) {
            /** @var ProductVariant $v */
            return $v->getId();
        }, $product->getVariants()->toArray());

        return parent::createDTO($product) + [
            self::PRODUCT_HAS_VARIANTS => $product->hasVariants(),
            self::PRODUCT_VARIANTS     => array_combine($variantIds, $variants),
        ];
    }
}