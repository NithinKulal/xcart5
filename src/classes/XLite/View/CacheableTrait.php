<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

/**
 * Enables caching for a widget
 */
trait CacheableTrait
{
    /**
     * Cache availability
     *
     * @return boolean
     */
    protected function isCacheAvailable()
    {
        return true;
    }

    /**
     * Get cache TTL (seconds)
     *
     * TODO: do we really need this 1 hour cache lifetime? can we use a bigger value? or even remove this at all?
     *
     * @return integer
     */
    protected function getCacheTTL()
    {
        return 3600;
    }
}
