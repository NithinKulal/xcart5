<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */


namespace XLite\Module\XC\FreeShipping\Logic\Order\Modifier;

/**
 * Decorate discount coupon modifier
 *
 * @Decorator\Depend("CDev\Coupons")
 */
class Coupon extends \XLite\Module\CDev\Coupons\Logic\Order\Modifier\Discount implements \XLite\Base\IDecorator
{
    /**
     * Return true if discount total is valid
     *
     * @return boolean
     */
    protected function isValidTotal($total)
    {
        return 0 <= $total;
    }
}
