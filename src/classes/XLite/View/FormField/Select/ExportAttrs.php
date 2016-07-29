<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Select;

/**
 * Export options: attributes selector
 */
class ExportAttrs extends \XLite\View\FormField\Select\Regular
{
    /**
     * Get default options list
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return array(
            'global'           => static::t('Global attributes'),
            'global_n_classes' => static::t('Global & Classes attributes'),
            'all'              => static::t('All attributes'),
            'none'             => static::t('No attributes'),
        );
    }
}
