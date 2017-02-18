<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */


namespace XLite\Module\XC\FreeShipping\Logic\Order\Modifier;

/**
 * Decorate shipping modifier
 *
 * @Decorator\Depend("CDev\Coupons")
 * @Decorator\After("XC\FreeShipping")
 */
class ShippingCoupon extends \XLite\Logic\Order\Modifier\Shipping implements \XLite\Base\IDecorator
{
    /**
     * Return true if order item must be excluded from shipping rates calculations
     *
     * @return boolean
     */
    protected function isIgnoreShippingCalculation($item)
    {
        return parent::isIgnoreShippingCalculation($item)
            || $this->isAppliedFreeShippingCoupon($item);
    }

    /**
     * Return true if free shipping coupon is applied to specified order item
     *
     * @param \XLite\Model\OrderItem $item Order item model
     *
     * @return boolean
     */
    protected function isAppliedFreeShippingCoupon($item)
    {
        $result = false;

        if ($this->order->getUsedCoupons()) {

            foreach ($this->order->getUsedCoupons() as $coupon) {

                if ($coupon->getCoupon() && $coupon->getCoupon()->isFreeShipping()) {
                    $result = $coupon->getCoupon()->isValidForProduct($item->getProduct());
                }

                if ($result) {
                    break;
                }
            }
        }

        return $result;
    }
}
