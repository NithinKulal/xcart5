<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\FreeShipping\Model\DTO\Product;

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
                'shipping' => [
                    'requires_shipping' => [
                        'free_shipping'          => $data->getFreeShip(),
                        'fixed_shipping_freight' => $data->getFreightFixedFee(),
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
        $freeShipping = static::deCompose($this, 'shipping', 'requires_shipping', 'free_shipping');
        $dataObject->setFreeShip($freeShipping);

        $fixedShippingFreight = static::deCompose($this, 'shipping', 'requires_shipping', 'fixed_shipping_freight');
        $dataObject->setFreightFixedFee($fixedShippingFreight);

        parent::populateTo($dataObject, $rawData);
    }
}
