<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Select;

/**
 * Weight unit selector
 */
class WeightUnit extends \XLite\View\FormField\Select\Regular
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

        $list['onchange'] = 'javascript: if (this.form.weight_symbol) { this.form.weight_symbol.value = this.value; }';

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
            'lbs' => static::t('LB'),
            'oz'  => static::t('OZ'),
            'kg'  => static::t('KG'),
            'g'   => static::t('G'),
        );
    }
}
