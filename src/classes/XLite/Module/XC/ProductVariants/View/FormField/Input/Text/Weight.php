<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductVariants\View\FormField\Input\Text;

/**
 * Weight
 */
class Weight extends \XLite\View\FormField\Input\Text\Weight
{
    /**
     * Sanitize value
     *
     * @return mixed
     */
    protected function sanitizeFloat($value)
    {
        return '' !== $value ? parent::sanitizeFloat($value) : $value;
    }

    /**
     * getCommonAttributes
     *
     * @return array
     */
    protected function getCommonAttributes()
    {
        $attributes = parent::getCommonAttributes();

        $attributes['value'] = '' !== $attributes['value']
            ? $attributes['value']
            : '';

        return $attributes;
    }
}
