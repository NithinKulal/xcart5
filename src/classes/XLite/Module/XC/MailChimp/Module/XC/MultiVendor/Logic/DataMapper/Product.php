<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MailChimp\Module\XC\MultiVendor\Logic\DataMapper;

/**
 * Class Product
 *
 * @Decorator\Depend ("XC\MultiVendor")
 */
class Product extends \XLite\Module\XC\MailChimp\Logic\DataMapper\Product implements \XLite\Base\IDecorator
{
    /**
     * @param \XLite\Model\OrderItem $item
     *
     * @return array
     */
    public static function getDataByOrderItem(\XLite\Model\OrderItem $item)
    {
        /** @var \XLite\Module\XC\MultiVendor\Model\OrderItem $item */
        $data = parent::getDataByOrderItem($item);
        
        if ($item->getVendor()) {
            $data['vendor'] = $item->getVendor()->getVendorCompanyName()
                ?: 'vendor';
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
        /** @var \XLite\Module\XC\MultiVendor\Model\Product $product */
        $data = parent::getDataByProduct($product);

        if ($product->getVendor()) {
            $data['vendor'] = $product->getVendor()->getVendorCompanyName()
                ?: 'vendor';
        }

        return $data;
    }
}