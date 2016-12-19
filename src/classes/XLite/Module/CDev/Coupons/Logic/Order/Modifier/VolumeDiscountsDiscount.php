<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Coupons\Logic\Order\Modifier;


/**
 * Hack for normal merge Coupons and VolumeDiscounts surcharges(to not exceed subtotal)
 * @Decorator\Depend ("CDev\VolumeDiscounts")
 */
class VolumeDiscountsDiscount extends \XLite\Module\CDev\Coupons\Logic\Order\Modifier\Discount implements \XLite\Base\IDecorator
{
    /**
     * Calculate
     *
     * @return \XLite\Model\Order\Surcharge
     */
    public function calculate()
    {
        $surcharge = null;

        $total = 0;

        foreach ($this->getUsedCoupons() as $used) {
            if ($used->getCoupon()) {
                $used->setValue($used->getCoupon()->getAmount($this->order));
            }
            $total += $used->getValue();

            if ($used->getCoupon()) {
                $this->distributeDiscountAmongItems(
                    $used->getValue(),
                    $this->getOrder()->getValidItemsByCoupon($used->getCoupon())
                );
            }
        }

        if ($this->isValidTotal($total)) {
            $subtotal = $this->order->getSubtotal();
            foreach ($this->getOrder()->getSurcharges() as $surcharge) {
                if ($surcharge->getClass() == 'XLite\Module\CDev\VolumeDiscounts\Logic\Order\Modifier\Discount') {
                    $subtotal += $surcharge->getValue();
                    break;
                }
            }
            $total = min($total, $subtotal);
            $surcharge = $this->addOrderSurcharge($this->code, $total * -1, false);
        }

        return $surcharge;
    }
}
