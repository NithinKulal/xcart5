<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\FreeShipping\View\ItemsList\Model;

/**
 * Coupons list
 *
 * @Decorator\Depend("CDev\Coupons")
 */
class Coupons extends \XLite\Module\CDev\Coupons\View\ItemsList\Coupons implements \XLite\Base\IDecorator
{
    /**
     * Preprocess value for Discount column
     *
     * @param mixed                                   $value  Value
     * @param array                                   $column Column data
     * @param \XLite\Module\CDev\Coupons\Model\Coupon $coupon Entity
     *
     * @return string
     */
    protected function preprocessValue($value, array $column, \XLite\Module\CDev\Coupons\Model\Coupon $coupon)
    {
        return $coupon->isFreeShipping()
            ? static::t('Free shipping')
            : parent::preprocessValue($value, $column, $coupon);
    }
}
