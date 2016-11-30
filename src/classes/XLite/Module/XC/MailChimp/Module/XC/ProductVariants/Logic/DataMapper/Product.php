<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MailChimp\Module\XC\ProductVariants\Logic\DataMapper;

use XLite\Module\XC\MailChimp\Logic\DataMapper\Variant;

/**
 * Class Product
 *
 * @Decorator\Depend ("XC\ProductVariants")
 */
class Product extends \XLite\Module\XC\MailChimp\Logic\DataMapper\Product implements \XLite\Base\IDecorator
{
    /**
     * @param \XLite\Module\XC\ProductVariants\Model\Product $product
     */
    protected static function getVariantsByProductData(\XLite\Model\Product $product)
    {
        if (!$product->hasVariants()) {
            return parent::getVariantsByProductData($product);
        }
        
        $result = [];

        /** @var \XLite\Module\XC\ProductVariants\Model\ProductVariant $variant */
        foreach ($product->getVariants() as $variant) {
            $result[] = Variant::getVariantDataByProductVariant($variant);
        }

        return $result;
    }

    /**
     * @param \XLite\Module\XC\ProductVariants\Model\OrderItem $item
     */
    protected static function getVariantsByOrderItemData(\XLite\Model\OrderItem $item)
    {
        if (!$item->getObject()) {
            return parent::getVariantsByOrderItemData($item);
        }

        return static::getVariantsByProductData($item->getObject());
    }

}