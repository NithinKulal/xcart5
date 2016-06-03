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
     * @param mixed|\XLite\Model\Product $data
     */
    protected function init($data)
    {
        parent::init($data);

        static::compose(
            $this,
            [
                'prices_and_inventory' => [
                    'price' => [
                        'participate_sale' => $data->getParticipateSale(),
                        'sale_price'      => [
                            'type'  => $data->getDiscountType(),
                            'value' => $data->getSalePriceValue(),
                        ],
                    ],
                ],
            ]
        );
    }

    /**
     * @param \XLite\Model\Product $dataObject
     * @param array|null           $rawData
     *
     * @return mixed
     */
    public function populateTo($dataObject, $rawData = null)
    {
        $participateSale = static::deCompose($this, 'prices_and_inventory', 'price', 'participate_sale');
        $dataObject->setParticipateSale($participateSale);

        $salePrice = static::deCompose($this, 'prices_and_inventory', 'price', 'sale_price');
        $dataObject->setDiscountType($salePrice['type']);
        $dataObject->setSalePriceValue($salePrice['value']);

        parent::populateTo($dataObject, $rawData);
    }
}
