<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\GoogleAnalytics\Module\XC\ProductVariants\Action;

/**
 * Class OrderItemDataMapper
 *
 * @Decorator\Depend ("XC\ProductVariants")
 */
class OrderItemDataMapper extends \XLite\Module\CDev\GoogleAnalytics\Logic\DataMapper\OrderItemDataMapper implements \XLite\Base\IDecorator
{
    /**
     * @param \XLite\Model\OrderItem $item
     * @param string                 $categoryName
     * @param null                   $qty
     *
     * @return array
     */
    public static function getData(\XLite\Model\OrderItem $item, $categoryName = '', $qty = null)
    {
        $result = parent::getData($item, $categoryName, $qty);

        if ($item->getVariant()) {
            $result['id']       = $item->getVariant()->getSku() ?: $result['id'];
            $result['price']    = is_numeric($item->getVariant()->getNetPrice())
                ? $item->getVariant()->getNetPrice()
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
    protected static function getVariant(\XLite\Model\OrderItem $item)
    {
        $variantName = parent::getVariant($item);

        $variant = $item->getVariant();

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