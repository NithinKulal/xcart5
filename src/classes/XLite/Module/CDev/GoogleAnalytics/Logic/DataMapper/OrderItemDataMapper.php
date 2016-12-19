<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\GoogleAnalytics\Logic\DataMapper;

/**
 * Class OrderItemDataMapper
 */
class OrderItemDataMapper
{
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
     *   'quantity': 1                     // Product quantity (number).
     * ]
     *
     * @param \XLite\Model\OrderItem $item
     * @param string                 $categoryName
     * @param null                   $qty
     *
     * @return array
     * @internal param \XLite\Model\Product $product
     * @internal param $shownInList
     *
     */
    public static function getData(\XLite\Model\OrderItem $item, $categoryName = '', $qty = null)
    {
        $brand      = static::getBrand($item);
        $variant    = static::getVariant($item);

        if (!$categoryName) {
            $defaultCategoryName = $item->getObject()->getCategory()
                ? $item->getObject()->getCategory()->getName()
                : '';
            $categoryName = $item->getCategoryAdded() ?: $defaultCategoryName;
        }

        return [
            'id'        => $item->getSku(),
            'name'      => $item->getObject()
                ? $item->getObject()->getName()
                : $item->getName(),
            'category'  => strval($categoryName),
            'brand'     => $brand,
            'variant'   => $variant,
            'price'     => strval($item->getNetPrice()),
            'quantity'  => $qty
                ? strval($qty)
                : strval($item->getAmount()),
        ];
    }

    /**
     * @param array $productData
     * @param int   $index
     *
     * @return array
     */
    public static function getDataForBackend(\XLite\Model\OrderItem $item, $index = 1, $qty = null)
    {
        $productData = static::getData($item, '', $qty);
        $result = [];

        $result["pr".$index."id"] = $productData['id'];
        $result["pr".$index."nm"] = $productData['name'];
        $result["pr".$index."ca"] = $productData['category'];
        $result["pr".$index."br"] = $productData['brand'];
        $result["pr".$index."va"] = $productData['variant'];
        $result["pr".$index."pr"] = $productData['price'];
        $result["pr".$index."qt"] = $productData['quantity'];

        return $result;
    }

    /**
     * Get product's variant
     *
     * @param \XLite\Model\OrderItem $item
     *
     * @return string
     */
    protected static function getVariant(\XLite\Model\OrderItem $item)
    {
        return '';
    }

    /**
     * Get product's brand
     * TODO Try to get brand. Its tricky because there is no service name for attributes
     *
     * @param \XLite\Model\OrderItem $item
     *
     * @return string
     */
    protected static function getBrand(\XLite\Model\OrderItem $item)
    {
        return '';
    }
}
