<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Select;

/**
 * Dimensional unit selector
 */
class DimUnit extends \XLite\View\FormField\Select\Regular
{
    /**
     * Set common attributes
     *
     * @param array $attrs Field attributes to prepare
     *
     * @return array
     */
    protected function setCommonAttributes(array $attrs)
    {
        $list = parent::setCommonAttributes($attrs);

        $list['onchange'] = 'javascript: if (this.form.dim_symbol) { this.form.dim_symbol.value = this.value; }';

        return $list;
    }

    /**
     * Get default options
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return array(
            'mm' => static::t('MM'),
            'cm' => static::t('CM'),
            'dm' => static::t('DM'),
            'm'  => static::t('M'),
            'in' => static::t('IN'),
        );
    }
}
