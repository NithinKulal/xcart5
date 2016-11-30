<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MailChimp\Logic\DataMapper;


use XLite\Module\XC\MailChimp\Main;

class Line
{
    /**
     * @param \XLite\Model\OrderItem $item
     *
     * @return array
     */
    public static function getDataByOrderItem(\XLite\Model\OrderItem $item)
    {
        try {
            list($categoryId, $categoryName) = static::getCategoryData(
                $item->getObject()->getCategories()
            );
            
            return [
                'id'                    => strval($item->getItemId()),
                'product_id'            => $item->getObject()
                    ? strval($item->getObject()->getProductId())
                    : strval($item->getItemId()),
                'product_variant_id'    => $item->getObject()
                    ? strval($item->getObject()->getProductId() . '_dv')
                    : strval($item->getItemId() . '_dv'),
                'sku'                   => $item->getSku(),
                'product_name'          => $item->getName(),
                'category_id'           => $categoryId,
                'category_name'         => $categoryName,
                'quantity'              => intval($item->getAmount()),
                'price'                 => $item->getOrder()->getCurrency()
                    ? $item->getOrder()->getCurrency()->formatValue($item->getItemNetPrice())
                    : $item->getItemNetPrice()
            ];
        } catch (\Exception $e) {
            Main::logError($e->getMessage(), []);
            return null;
        }
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