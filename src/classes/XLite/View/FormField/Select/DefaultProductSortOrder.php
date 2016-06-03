<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Select;

/**
 * Default products sort order selector
 */
class DefaultProductSortOrder extends \XLite\View\FormField\Select\Regular
{
    /**
     * Get default options
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return array(
            'default'   => static::t('Recommended'),
            'priceAsc'  => static::t('Price asc'),
            'priceDesc' => static::t('Price desc'),
            'nameAsc'   => static::t('Name asc'),
            'nameDesc'  => static::t('Name desc'),
        );
    }
}
