<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Concierge\View\ItemsList\Model\Shipping;

use XLite\Module\XC\Concierge\Core\Mediator;
use XLite\Module\XC\Concierge\Core\Track\ShippingMethod;

abstract class Carriers extends \XLite\View\ItemsList\Model\Shipping\Carriers implements \XLite\Base\IDecorator
{
    /**
     * @param \XLite\Model\AEntity $entity
     *
     * @return boolean
     */
    protected function removeEntity(\XLite\Model\AEntity $entity)
    {
        $result = parent::removeEntity($entity);

        if ($result && ('offline' === $entity->getProcessor() && '' !== $entity->getCarrier())) {
            Mediator::getInstance()->addMessage(
                new ShippingMethod('Remove Shipping Method', $entity)
            );
        }

        return $result;
    }
}
