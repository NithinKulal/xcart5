<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Base;

/**
 * Enables caching for a widget
 */
trait ViewListsFallbackTrait
{
    /**
     * getViewListChildren
     *
     * @param string $list List name
     *
     * @return array
     */
    protected function getViewListChildren($list)
    {
        return \XLite\Core\Database::getRepo('XLite\Model\ViewList')->findClassListWithFallback(
            $list,
            static::detectCurrentViewZone()
        );
    }
}
