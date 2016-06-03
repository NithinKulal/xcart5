<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\VolumeDiscounts\View\FormField;

/**
 * Discount type (% or $) selector widget
 */
class SelectDiscountType extends \XLite\View\FormField\Select\Regular
{
    /**
     * Available options
     */
    const VALUE_PERCENT  = '%';
    const VALUE_ABSOLUTE = '$';


    /**
     * getDefaultOptions
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return array(
            self::VALUE_PERCENT  => '%',
            self::VALUE_ABSOLUTE => \XLite::getInstance()->getCurrency()->getCurrencySymbol(),
        );
    }
}
