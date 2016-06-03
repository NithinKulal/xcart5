<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Sale\Logic\BulkEdit\Field\Product;

/**
 * @Decorator\Depend ("XC\BulkEditing")
 */
class SalePrice extends \XLite\Module\XC\BulkEditing\Logic\BulkEdit\Field\AField
{
    public static function getSchema($name, $options)
    {
        return [
            $name => [
                'label'    => static::t('Sale price'),
                'type'     => 'XLite\Module\CDev\Sale\View\FormModel\Type\Sale',
                'position' => isset($options['position']) ? $options['position'] : 0,
            ],
        ];
    }

    public static function getData($name, $object)
    {
        return [
            $name => [
                'type'  => \XLite\Model\Product::SALE_DISCOUNT_TYPE_PERCENT,
                'value' => 0,
            ],
        ];
    }

    public static function populateData($name, $object, $data)
    {
        $sale = $data->{$name};
        $object->setDiscountType($sale['type']);
        $object->setSalePriceValue($sale['value']);
    }

    /**
     * @param string               $name
     * @param \XLite\Model\Product $object
     * @param array                $options
     *
     * @return array
     */
    public static function getViewData($name, $object, $options)
    {
        $participateSale = $object->getParticipateSale();

        return $participateSale
            ? [
                $name => [
                    'label'    => static::t('Sale'),
                    'value'    => $object->getDiscountType() === \XLite\Model\Product::SALE_DISCOUNT_TYPE_PRICE
                        ? \XLite\View\AView::formatPrice($object->getSalePriceValue())
                        : $object->getSalePriceValue() . '%',
                    'position' => isset($options['position']) ? $options['position'] : 0,
                ],
            ]
            : [];
    }
}
