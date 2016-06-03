<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\FreeShipping\View\FormField;

/**
 * Discount type selector
 *
 * @Decorator\Depend("CDev\Coupons")
 */
class DiscountType extends \XLite\Module\CDev\Coupons\View\FormField\DiscountType implements \XLite\Base\IDecorator
{
    /**
     * Get default options
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        $options = parent::getDefaultOptions();
        $options[\XLite\Module\CDev\Coupons\Model\Coupon::TYPE_FREESHIP] = static::t('Free shipping');

        return $options;
    }
}
