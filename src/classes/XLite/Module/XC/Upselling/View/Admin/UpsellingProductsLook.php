<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Upselling\View\Admin;

/**
 * Related products widget look selector
 */
class UpsellingProductsLook extends \XLite\View\FormField\Select\Regular
{
    /**
     * Get default options
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return array(
            'list'  => static::t('List'),
            'grid'  => static::t('Grid'),
            'table' => static::t('Table'),
        );
    }
}
