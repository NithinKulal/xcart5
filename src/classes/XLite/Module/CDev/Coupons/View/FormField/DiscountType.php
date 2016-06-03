<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Coupons\View\FormField;

/**
 * Discount type selector
 */
class DiscountType extends \XLite\View\FormField\Select\Regular
{
    /**
     * Get default options
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return array(
            '%' => static::t('Percent'),
            '$' => static::t('X off', array('currency' => \XLite::getInstance()->getCurrency()->getCurrencySymbol())),
        );
    }
}
