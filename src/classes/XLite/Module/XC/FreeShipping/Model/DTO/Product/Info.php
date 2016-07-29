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
     * @param mixed|\XLite\Model\Product $object
     */
    protected function init($object)
    {
        parent::init($object);

        static::compose(
            $this,
            [
                'shipping' => [
                    'requires_shipping' => [
                        'free_shipping'          => $object->getFreeShip(),
                        'fixed_shipping_freight' => $object->getFreightFixedFee(),
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
        $freeShipping = static::deCompose($this, 'shipping', 'requires_shipping', 'free_shipping');
        $object->setFreeShip($freeShipping);

        $fixedShippingFreight = static::deCompose($this, 'shipping', 'requires_shipping', 'fixed_shipping_freight');
        $object->setFreightFixedFee($fixedShippingFreight);

        parent::populateTo($object, $rawData);
    }
}
