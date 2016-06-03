<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Coupons\View;

/**
 * Cart
 */
abstract class Cart extends \XLite\View\Cart implements \XLite\Base\IDecorator
{
    /**
     * Discount coupons (local cache)
     *
     * @var array
     */
    protected $discountCoupons;

    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/CDev/Coupons/cart.css';

        return $list;
    }

    /**
     * Check - discount coupon subpanel is visible or not
     *
     * @param array $surcharge Surcharge
     *
     * @return boolean
     */
    protected function isDiscountCouponSubpanelVisible(array $surcharge)
    {
        return 'dcoupon' === strtolower($surcharge['code']) && $this->getDiscountCoupons();
    }

    /**
     * Get coupons
     *
     * @return array
     */
    protected function getDiscountCoupons()
    {
        if (null === $this->discountCoupons) {
            $this->discountCoupons = $this->getCart()->getUsedCoupons()->toArray();
        }

        return $this->discountCoupons;
    }

    /**
     * Check discount coupon remove control is visible or not
     *
     * @return boolean
     */
    protected function isDiscountCouponRemoveVisible()
    {
        return true;
    }
}
