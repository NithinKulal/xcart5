<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductVariants\View\FormField\Select;

/**
 * Class PriceInList
 */
class PriceInList extends \XLite\View\FormField\Select\Regular
{
    const DISPLAY_DEFAULT = 'D';
    const DISPLAY_RANGE = 'R';

    /**
     * Get default options
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return [
            static::DISPLAY_DEFAULT => static::t('Default variant price'),
            static::DISPLAY_RANGE => static::t('Price range (min - max)'),
        ];
    }
}