<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Coupons\Model;

/**
 * Membership
 */
abstract class Membership extends \XLite\Model\Membership implements \XLite\Base\IDecorator
{
    /**
     * Coupons
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ManyToMany (targetEntity="XLite\Module\CDev\Coupons\Model\Coupon", mappedBy="memberships")
     */
    protected $coupons;

    /**
     * Add coupons
     *
     * @param \XLite\Module\CDev\Coupons\Model\Coupon $coupons
     * @return Membership
     */
    public function addCoupons(\XLite\Module\CDev\Coupons\Model\Coupon $coupons)
    {
        $this->coupons[] = $coupons;
        return $this;
    }

    /**
     * Get coupons
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCoupons()
    {
        return $this->coupons;
    }
}
