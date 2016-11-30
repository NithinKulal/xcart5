<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\GoogleAnalytics\Module\XC\ProductVariants\Action;

/**
 * Class ProductDataMapper
 *
 * @Decorator\Depend ("XC\ProductVariants")
 */
class ProductDataMapper extends \XLite\Module\CDev\GoogleAnalytics\Logic\DataMapper\ProductDataMapper implements \XLite\Base\IDecorator
{
    public static function getAddProductData(\XLite\Model\Product $product, $categoryName = '', $coupon = '', $positionInList = '')
    {
        $result = parent::getAddProductData($product, $categoryName, $coupon, $positionInList);

        if ($product->getVariant()) {
            $result['id']       = $product->getVariant()->getSku() ?: $result['id'];
            $result['price']    = is_numeric($product->getVariant()->getNetPrice())
                ? $product->getVariant()->getNetPrice()
                : $result['price'];
        }

        return $result;
    }

    /**
     * Get product's variant
     *
     * @param \XLite\Model\OrderItem $item
     *
     * @return string
     */
    protected static function getVariant(\XLite\Model\Product $product)
    {
        $variantName = parent::getVariant($product);

        $variant = $product->getVariant();

        if ($variant) {

            $hash = array();
            foreach ($variant->getValues() as $av) {
                $hash[] = $av->getAttribute()->getName() . ':' . $av->asString();
            }
            sort($hash);
            $variantName = implode('_', $hash);
        }

        return $variantName;
    }
}