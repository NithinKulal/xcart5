<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MailChimp\Logic\DataMapper;


class Variant
{
    /**
     * @param \XLite\Model\OrderItem $item
     *
     * @return array
     */
    public static function getVariantDataByOrderItem(\XLite\Model\OrderItem $item)
    {
        $data = [
            'id'                    => strval($item->getItemId()) . '_dv',
            'title'                 => $item->getName() ?: '',
            'url'                   => '',
            'sku'                   => $item->getSku(),
            'price'                 => $item->getNetPrice(),
            'inventory_quantity'    => 1,
            'image_url'             => $item->getImageURL() ?: '',
        ];
        
        if ($item->getObject()) {
            $data = array_merge(
                $data,
                static::getVariantDataByProduct($item->getObject())
            );
        }
        
        return $data;
    }

    /**
     * @param \XLite\Model\Product $product
     *
     * @return array
     */
    public static function getVariantDataByProduct(\XLite\Model\Product $product)
    {
        return [
            'id'                    => strval($product->getProductId()) . '_dv',
            'title'                 => $product->getName() ?: '',
            'url'                   => $product->getFrontURLForMailChimp(),
            'sku'                   => $product->getSku(),
            'price'                 => $product->getNetPrice(),
            'inventory_quantity'    => $product->getQty(),
            'image_url'             => $product->getImageURL() ?: '',
        ];
    }
}