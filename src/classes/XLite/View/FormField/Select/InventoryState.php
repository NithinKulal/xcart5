<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Select;

/**
 * Inventory state selector
 */
class InventoryState extends \XLite\View\FormField\Select\ASelect
{
    /**
     * Return default options list
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return array(
            'all' => static::t('Any stock status'),
            'in'  => static::t('In stock'),
            'low' => static::t('Low stock'),
            'out' => static::t('Out of stock'),
        );
    }

}
