<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MailChimp\Logic\DataMapper;


class Product
{
    /**
     * @param \XLite\Model\OrderItem $item
     *
     * @return array
     */
    public static function getDataByOrderItem(\XLite\Model\OrderItem $item)
    {
        $data = [
            'id'                    => strval($item->getItemId()),
            'title'                 => $item->getName() ?: '',
            'url'                   => '',
            'description'           => '',
            'vendor'                => 'admin',                             // TODO integration with XC\MultiVendor
            'image_url'             => $item->getImageURL() ?: '',
            'variants'              => static::getVariantsByOrderItemData($item),
        ];
        
        if ($item->getObject()) {
            // Replace with product data if available
            $data = array_merge(
                $data,
                static::getDataByProduct($item->getObject())
            );
        }
        
        return $data;
    }

    /**
     * @param \XLite\Model\Product $product
     *
     * @return array
     */
    public static function getDataByProduct(\XLite\Model\Product $product)
    {
        return [
            'id'                    => strval($product->getProductId()),
            'title'                 => $product->getName() ?: '',
            'url'                   => $product->getFrontURLForMailChimp() ?: '',
            'description'           => $product->getBriefDescription() ?: '',
            'vendor'                => 'admin',                             // TODO integration with XC\MultiVendor
            'image_url'             => $product->getImageURL() ?: '',
            'variants'              => static::getVariantsByProductData($product),
            'published_at_foreign'  => date('c', time()),
        ];
    }

    /**
     * @param \XLite\Model\Product $product
     */
    protected static function getVariantsByProductData(\XLite\Model\Product $product)
    {
        return [
            Variant::getVariantDataByProduct($product),
        ];
    }

    /**
     * @param \XLite\Model\OrderItem $item
     */
    protected static function getVariantsByOrderItemData(\XLite\Model\OrderItem $item)
    {
        return [
            Variant::getVariantDataByOrderItem($item),
        ];
    }

    /**
     * Get category data
     * 
     * @param \XLite\Model\Category[] $categories
     * @return array
     */
    protected static function getCategoryData(array $categories)
    {
        if (!empty($categories)) {
            $categoryId = $categories[0]->getId();
            $categoryName = $categories[0]->getStringPath();
        } else {
            $categoryId = 0;
            $categoryName = '';
        }
        
        return [ $categoryId, $categoryName ];
    }
}