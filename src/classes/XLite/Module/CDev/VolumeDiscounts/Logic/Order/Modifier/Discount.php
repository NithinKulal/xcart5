<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\VolumeDiscounts\Logic\Order\Modifier;

/**
 * Value discount modifier
 */
class Discount extends \XLite\Logic\Order\Modifier\Discount
{
    /**
     * Modifier code is the same as a base Discount - this will be aggregated
     * to the single 'Discount' line in cart totals
     */
    const MODIFIER_CODE = 'DISCOUNT';

    /**
     * Modifier type (see \XLite\Model\Base\Surcharge)
     *
     * @var   string
     */
    protected $type = \XLite\Model\Base\Surcharge::TYPE_DISCOUNT;

    /**
     * Modifier unique code
     *
     * @var   string
     */
    protected $code = self::MODIFIER_CODE;

    // {{{ Calculation

    /**
     * Check - can apply this modifier or not
     *
     * @return boolean
     */
    public function canApply()
    {
        return parent::canApply()
            && $this->hasDiscount();
    }

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
                $total = min($total, $this->getOrder()->getSubtotal());
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
     * Check for suitable discount
     *
     * @return boolean
     */
    protected function hasDiscount()
    {
        $discount = $this->getDiscount();

        return $discount && 0 < $discount->getAmount($this->order);
    }

    /**
     * Get suitable discount from database
     *
     * @return \XLite\Module\CDev\VolumeDiscounts\Model\VolumeDiscount
     */
    protected function getDiscount()
    {
        /** @var \XLite\Module\CDev\VolumeDiscounts\Model\Repo\VolumeDiscount $repo */
        $repo = \XLite\Core\Database::getRepo('XLite\Module\CDev\VolumeDiscounts\Model\VolumeDiscount');

        return $repo->getFirstDiscount($this->getDiscountCondition());
    }

    /**
     * Returns discount condition
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getDiscountCondition()
    {
        $cnd = new \XLite\Core\CommonCell();

        $cnd->{\XLite\Module\CDev\VolumeDiscounts\Model\Repo\VolumeDiscount::P_SUBTOTAL}
            = $this->getOrder()->getSubtotal();

        $profile = $this->getOrder()->getProfile();
        $membership = $profile ? $profile->getMembership() : null;
        if ($membership) {
            $cnd->{\XLite\Module\CDev\VolumeDiscounts\Model\Repo\VolumeDiscount::P_MEMBERSHIP}
                = $membership;
        }

        return $cnd;
    }

    // }}}
}
