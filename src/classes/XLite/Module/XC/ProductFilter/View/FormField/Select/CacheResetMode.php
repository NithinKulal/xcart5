<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductFilter\View\FormField\Select;

/**
 * 'Cache reset mode' selector
 */
class CacheResetMode extends \XLite\View\FormField\Select\Regular
{
    /**
     * Get default options
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return array(
            0  => static::t('Generate cache on the fly'),
            1  => static::t('Remove cache when attribute, tag or product data is changed'),
        );
    }
}
