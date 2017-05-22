<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2017-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/module-marketplace-terms-of-use.html for license details.
 */

namespace XLite\Module\QSL\SpecialOffersBase\Module\CDev\VolumeDiscounts\Logic\Order\Modifier;

/**
 * Value discount modifier
 * 
 * @Decorator\Depend ("CDev\VolumeDiscounts")
 */
class Discount extends \XLite\Module\CDev\VolumeDiscounts\Logic\Order\Modifier\Discount
               implements \XLite\Base\IDecorator
{
    /**
     * Calculate
     *
     * @return float
     */
    public function calculate()
    {
        $surcharge = null;

        $discount = $this->getDiscount();

        if ($discount) {
            $total = $discount->getAmount($this->order);

            if ($total) {
                $total = min($total, $this->getSpecialOfferSubtotal());
                $surcharge = $this->addOrderSurcharge($this->code, $total * -1, false);

                // Distribute discount value among the ordered products
                $this->distributeDiscount($total);
            }

        } else {
            $discount = null;
        }

        return $surcharge;
    }

    /**
     * Returns discount condition
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getDiscountCondition()
    {
        $cnd = parent::getDiscountCondition();

        $cnd->{\XLite\Module\CDev\VolumeDiscounts\Model\Repo\VolumeDiscount::P_SUBTOTAL}
            = $this->getSpecialOfferSubtotal();

        return $cnd;
    }
    
    /**
     * Returns the order subtotal plus order item surcharges.
     * 
     * @return float
     */
    protected function getSpecialOfferSubtotal()
    {
        return $this->getOrder()->getSubtotal()
            + \XLite\Core\Database::getInstance()->getRepo('XLite\Model\OrderItem')
                ->getSpecialOffersOrderItemSurchargesSum($this->getOrder());
    }

}
