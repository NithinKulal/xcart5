<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\GoogleAnalytics\Logic\DataMapper;

/**
 * Class ProductDataMapper
 */
class ProductDataMapper
{
    /**
     * Get impression GA event data
     *
     * Example:
     * [
     *   'id'         => 'P12345',                            // Product ID (string).
     *   'name'       => 'Android Warhol T-Shirt',            // Product name (string).
     *   'category'   => 'Apparel/T-Shirts',                  // Product category (string).
     *   'brand'      => 'Google',                            // Product brand (string).
     *   'variant'    => 'Black',                             // Product variant (string).
     *   'list'       => 'Related Products',                  // Product list (string).
     *   'position'   => 1,                                   // Product position (number).
     *   ]
     *
     * @param \XLite\Model\Product $product
     * @param                      $categoryName
     * @param                      $shownInList
     *
     * @return array
     */
    public static function getImpressionData(\XLite\Model\Product $product, $categoryName, $shownInList = '', $positionInList = '')
    {
        return array_merge(
            static::getCommonData($product, $categoryName, $positionInList),
            [
                'list' => $shownInList,
            ]
        );
    }

    /**
     * Get impression GA event data
     *
     * Example:
     * [
     *   'id': 'P12345',                   // Product ID (string).
     *   'name': 'Android Warhol T-Shirt', // Product name (string).
     *   'category': 'Apparel',            // Product category (string).
     *   'brand': 'Google',                // Product brand (string).
     *   'variant': 'black',               // Product variant (string).
     *   'price': '29.20',                 // Product price (currency).
     *   'quantity': 1,                    // Product quantity (number)
     *   'coupon': 'APPARELSALE',          // Product coupon (string).
     *   'quantity': 1                     // Product quantity (number).
     * ]
     *
     * @param \XLite\Model\Product $product
     * @param                      $categoryName
     * @param                      $shownInList
     *
     * @return array
     */
    public static function getAddProductData(\XLite\Model\Product $product, $categoryName = '', $coupon = '', $positionInList = '')
    {
        return array_merge(
            static::getCommonData($product, $categoryName, $positionInList),
            [
                'price'  => $product->getNetPrice(),
                'coupon' => $coupon,
            ]
        );
    }

    /**
     * @param \XLite\Model\Product $product
     * @param                      $categoryName
     * @param                      $positionInList
     *
     * @return array
     */
    protected static function getCommonData(\XLite\Model\Product $product, $categoryName, $positionInList)
    {
        $brand      = static::getBrand($product);
        $variant    = static::getVariant($product);

        return [
            'id'       => $product->getSku(),
            'name'     => $product->getName(),
            'category' => $categoryName,
            'brand'    => $brand,
            'variant'  => $variant,
            'position' => $positionInList,
        ];
    }

    /**
     * Get product's variant
     *
     * @param \XLite\Model\Product $product
     *
     * @return string
     */
    protected static function getVariant(\XLite\Model\Product $product)
    {
        return '';
    }

    /**
     * Get product's brand
     * TODO Try to get brand. Its tricky because there is no service name for attributes
     *
     * @param \XLite\Model\Product $product
     *
     * @return string
     */
    protected static function getBrand(\XLite\Model\Product $product)
    {
        return '';
    }
}