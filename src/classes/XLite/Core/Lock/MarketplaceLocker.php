<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Lock;

/**
 * Marketplace locker
 */
class MarketplaceLocker extends \XLite\Core\Lock\AObjectCacheLocker
{
    /**
     * Default TTL (1 minute)
     */
    const TTL = 60;

    /**
     * Default TTL to wait until lock released (in seconds)
     *
     * @var integer
     */
    const TTL_WAIT = 3;

    /**
     * Get object identifier
     *
     * @param string $object Object identifier (action name)
     *
     * @return string
     */
    protected function getIdentifier($object)
    {
        return $object;
    }
}
