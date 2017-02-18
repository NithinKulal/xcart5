<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Concierge\View\ItemsList\Model;

use XLite\Module\XC\Concierge\Core\Mediator;
use XLite\Module\XC\Concierge\Core\Track\Category as CategoryTrack;

/**
 * Category list
 */
abstract class Category extends \XLite\View\ItemsList\Model\Category implements \XLite\Base\IDecorator
{
    /**
     * @param \XLite\Model\AEntity $entity
     *
     * @return boolean
     */
    protected function removeEntity(\XLite\Model\AEntity $entity)
    {
        $result = parent::removeEntity($entity);
        if ($result) {
            Mediator::getInstance()->addMessage(
                new CategoryTrack(
                    'Remove Category',
                    $entity
                )
            );
        }

        return $result;
    }
}
