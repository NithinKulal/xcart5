<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Sale\Model\DTO\Product;

/**
 * Product
 */
class Info extends \XLite\Model\DTO\Product\Info implements \XLite\Base\IDecorator
{
    /**
     * @param mixed|\XLite\Model\Product $object
     */
    protected function init($object)
    {
        parent::init($object);

        static::compose(
            $this,
            [
                'prices_and_inventory' => [
                    'price' => [
                        'participate_sale' => $object->getParticipateSale(),
                        'sale_price'      => [
                            'type'  => $object->getDiscountType(),
                            'value' => $object->getSalePriceValue(),
                        ],
                    ],
                ],
            ]
        );
    }

    /**
     * @param \XLite\Model\Product $object
     * @param array|null           $rawData
     *
     * @return mixed
     */
    public function populateTo($object, $rawData = null)
    {
        $participateSale = static::deCompose($this, 'prices_and_inventory', 'price', 'participate_sale');
        $object->setParticipateSale((boolean) $participateSale);

        $salePrice = static::deCompose($this, 'prices_and_inventory', 'price', 'sale_price');
        $object->setDiscountType((string) $salePrice['type']);
        $object->setSalePriceValue((float) $salePrice['value']);

        parent::populateTo($object, $rawData);
    }
}
