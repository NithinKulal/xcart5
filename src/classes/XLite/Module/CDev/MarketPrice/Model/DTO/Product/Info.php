<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\MarketPrice\Model\DTO\Product;

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
                        'market_price' => $object->getMarketPrice(),
                    ]
                ]
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
        $marketPrice = static::deCompose($this, 'prices_and_inventory', 'price', 'market_price');
        $object->setMarketPrice((float) $marketPrice);

        parent::populateTo($object, $rawData);
    }
}
