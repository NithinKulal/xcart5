<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Concierge\View\ItemsList\Model\Product\Admin;

use XLite\Module\XC\Concierge\Core\Mediator;
use XLite\Module\XC\Concierge\Core\Track\Product;

/**
 * Abstract admin-interface products list
 */
abstract class AAdmin extends \XLite\View\ItemsList\Model\Product\Admin\AAdmin implements \XLite\Base\IDecorator
{
    /**
     * @inheritdoc
     */
    protected function removeEntity(\XLite\Model\AEntity $entity)
    {
        $result = parent::removeEntity($entity);
        if ($result) {
            Mediator::getInstance()->addMessage(
                new Product(
                    'Remove Product',
                    $entity
                )
            );
        }

        return $result;
    }
}
