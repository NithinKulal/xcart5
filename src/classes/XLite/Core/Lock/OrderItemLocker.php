<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Lock;

/**
 * OrderItem locker
 */
class OrderItemLocker extends \XLite\Core\Lock\AObjectCacheLocker
{
    /**
     * @param \XLite\Module\OrderItem $object OrderItem entity
     *
     * @return string
     */
    protected function getIdentifier($object)
    {
        return $object->getItemId();
    }
}
