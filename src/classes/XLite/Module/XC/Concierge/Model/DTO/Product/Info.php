<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Concierge\Model\DTO\Product;

use XLite\Module\XC\Concierge\Core\Mediator;
use XLite\Module\XC\Concierge\Core\Track\Product;

/**
 * Product
 */
abstract class Info extends \XLite\Model\DTO\Product\Info implements \XLite\Base\IDecorator
{
    /**
     * @param \XLite\Model\Product $object
     * @param array|null           $rawData
     *
     * @return mixed
     */
    public function afterUpdate($object, $rawData = null)
    {
        parent::afterUpdate($object, $rawData);

        Mediator::getInstance()->addMessage(
            new Product(
                'Update Product',
                $object
            )
        );
    }

    /**
     * @param \XLite\Model\Product $object
     * @param array|null           $rawData
     *
     * @return mixed
     */
    public function afterCreate($object, $rawData = null)
    {
        parent::afterCreate($object, $rawData);

        Mediator::getInstance()->addMessage(
            new Product(
                'Create Product',
                $object
            )
        );
    }
}
