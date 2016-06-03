<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductVariants\View\FormField\Input\Text;

/**
 * Price
 */
class Price extends \XLite\View\FormField\Input\Text\FloatInput
{
    /**
     * Get default E
     *
     * @return integer
     */
    static protected function getDefaultE()
    {
        return \XLite::getInstance()->getCurrency()->getE();
    }

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
            ? parent::sanitizeFloat($attributes['value'])
            : '';

        return $attributes;
    }
}
