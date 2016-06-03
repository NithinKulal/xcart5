<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Coupons\Logic\Order\Modifier;

/**
 * Discount coupons modifier
 */
class Discount extends \XLite\Logic\Order\Modifier\Discount
{
    const MODIFIER_CODE = 'DCOUPON';

    /**
     * Modifier unique code
     *
     * @var   string
     */
    protected $code = self::MODIFIER_CODE;

    // {{{ Widget

    /**
     * Get widget class
     *
     * @return string
     */
    public static function getWidgetClass()
    {
        return '\XLite\Module\CDev\Coupons\View\Order\Details\Admin\Modifier\DiscountCoupon';
    }

    // }}}

    // {{{ Calculation

    /**
     * Check - can apply this modifier or not
     *
     * @return boolean
     */
    public function canApply()
    {
        return parent::canApply()
            && $this->checkCoupons();
    }

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

            $this->distributeDiscountAmongItems(
                $used->getValue(),
                $this->getOrder()->getValidItemsByCoupon($used->getCoupon())
            );
        }

        if ($this->isValidTotal($total)) {
            $total = min($total, $this->order->getSubtotal());
            $surcharge = $this->addOrderSurcharge($this->code, $total * -1, false);
        }

        return $surcharge;
    }

    /**
     * Return true if discount total is valid
     *
     * @param float $total Total
     *
     * @return boolean
     */
    protected function isValidTotal($total)
    {
        return 0 < $total;
    }

    /**
     * Check coupons
     *
     * @throws \Doctrine\ORM\ORMInvalidArgumentException
     *
     * @return boolean
     */
    protected function checkCoupons()
    {
        foreach ($this->getUsedCoupons() as $used) {
            if (!$used->getCoupon()
                || !$used->getCoupon()->isActive($this->order)
            ) {
                $this->getUsedCoupons()->removeElement($used);
                \XLite\Core\Database::getEM()->remove($used);
            }
        }

        return 0 < count($this->getUsedCoupons());
    }

    /**
     * Get used coupons
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    protected function getUsedCoupons()
    {
        return $this->order->getUsedCoupons();
    }

    // }}}

    // {{{ Content helpers

    /**
     * Get surcharge name
     *
     * @param \XLite\Model\Base\Surcharge $surcharge Surcharge
     *
     * @return \XLite\DataSet\Transport\Order\Surcharge
     */
    public function getSurchargeInfo(\XLite\Model\Base\Surcharge $surcharge)
    {
        $info = new \XLite\DataSet\Transport\Order\Surcharge;
        $info->name = static::t('Coupon discount');

        return $info;
    }

    // }}}
}
