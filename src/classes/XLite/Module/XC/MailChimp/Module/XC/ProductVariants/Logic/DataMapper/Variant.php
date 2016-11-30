<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MailChimp\Module\XC\ProductVariants\Logic\DataMapper;

/**
 * Class Product
 *
 * @Decorator\Depend ("XC\ProductVariants")
 */
class Variant extends \XLite\Module\XC\MailChimp\Logic\DataMapper\Variant implements \XLite\Base\IDecorator
{
    /**
     * @param \XLite\Module\XC\ProductVariants\Model\ProductVariant $variant
     *
     * @return array
     */
    public static function getVariantDataByProductVariant(\XLite\Module\XC\ProductVariants\Model\ProductVariant $variant)
    {
        $imageUrl = $variant->getImage()
            ? $variant->getImage()->getFrontURL()
            : $variant->getProduct()->getImageURL();

        return [
            'id'                    => strval($variant->getId()),
            'title'                 => $variant->getProduct()->getName(),
            'url'                   => $variant->getFrontURLForMailChimp(),
            'sku'                   => $variant->getDisplaySku(),
            'price'                 => $variant->getNetPrice(),
            'inventory_quantity'    => $variant->getPublicAmount(),
            'image_url'             => $imageUrl ?: ''
        ];
    }
}