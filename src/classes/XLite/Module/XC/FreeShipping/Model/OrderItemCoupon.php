<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */


namespace XLite\Module\XC\FreeShipping\Model;

/**
 * Decorate OrderItem model
 *
 * @Decorator\Depend("CDev\Coupons")
 * @Decorator\After("XC\FreeShipping")
 */
class OrderItemCoupon extends \XLite\Model\OrderItem implements \XLite\Base\IDecorator
{
    /**
     * Return true if order item is forced to be 'free shipping' item
     *
     * @return boolean
     */
    public function isFreeShipping()
    {
        $result = parent::isFreeShipping();

        if (!$result && $this->getOrder()->getUsedCoupons()) {
            foreach ($this->getOrder()->getUsedCoupons() as $coupon) {
                if (!$coupon->isDeleted()
                    && $coupon->getCoupon()->isFreeShipping()
                    && $coupon->getCoupon()->isValidForProduct($this->getProduct())
                ) {
                    // Product is affected by discount coupon 'FREE SHIPPING'
                    $result = true;
                    break;
                }
            }
        }

        return $result;
    }
}
