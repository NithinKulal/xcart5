<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Coupons\Model;

/**
 * Order
 */
abstract class Order extends \XLite\Model\Order implements \XLite\Base\IDecorator
{
    /**
     * Used coupons
     *
     * @var \Doctrine\Common\Collections\Collection
     *
     * @OneToMany (targetEntity="XLite\Module\CDev\Coupons\Model\UsedCoupon", mappedBy="order", cascade={"all"})
     */
    protected $usedCoupons;

    /**
     * Constructor
     *
     * @param array $data Entity properties OPTIONAL
     */
    public function __construct(array $data = array())
    {
        $this->usedCoupons = new \Doctrine\Common\Collections\ArrayCollection();

        parent::__construct($data);
    }

    /**
     * Clone order and all related data
     *
     * @return \XLite\Model\Order
     */
    public function cloneEntity()
    {
        $newOrder = parent::cloneEntity();

        foreach ($this->getUsedCoupons() as $usedCoupon) {
            $cloned = $usedCoupon->cloneEntity();
            $cloned->setOrder($newOrder);
            $newOrder->addUsedCoupons($cloned);
            if ($usedCoupon->getCoupon()) {
                $cloned->setCoupon($usedCoupon->getCoupon());
                $usedCoupon->getCoupon()->addUsedCoupons($cloned);
            }
        }

        return $newOrder;
    }

    /**
     * Define fingerprint keys
     *
     * @return array
     */
    protected function defineFingerprintKeys()
    {
        $list = parent::defineFingerprintKeys();
        $list[] = 'coupons';

        return $list;
    }

    /**
     * Get fingerprint by 'items' key
     *
     * @return array
     */
    protected function getFingerprintByCoupons()
    {
        $coupons = array();
        foreach ($this->getUsedCoupons() as $coupon) {
            $coupons[] = $coupon->getCoupon()->getId();
        }

        return $coupons;
    }

    // {{{ Coupons manipulation

    /**
     * Add coupon
     *
     * @param \XLite\Module\CDev\Coupons\Model\Coupon $coupon Coupon
     *
     * @throws \Doctrine\ORM\ORMInvalidArgumentException
     *
     * @return void
     */
    public function addCoupon(\XLite\Module\CDev\Coupons\Model\Coupon $coupon)
    {
        $usedCoupon = new \XLite\Module\CDev\Coupons\Model\UsedCoupon();

        $usedCoupon->setOrder($this);
        $this->addUsedCoupons($usedCoupon);

        $usedCoupon->setCoupon($coupon);
        $coupon->addUsedCoupons($usedCoupon);

        \XLite\Core\Database::getEM()->persist($usedCoupon);
    }

    /**
     * Remove coupon
     *
     * @param \XLite\Module\CDev\Coupons\Model\UsedCoupon $usedCoupon Used coupon
     *
     * @throws \Doctrine\ORM\ORMInvalidArgumentException
     *
     * @return void
     */
    public function removeUsedCoupon(\XLite\Module\CDev\Coupons\Model\UsedCoupon $usedCoupon)
    {
        if ($this->getUsedCoupons()->removeElement($usedCoupon)) {
            \XLite\Core\Database::getEM()->remove($usedCoupon);
        }
    }

    /**
     * Check if coupon already present
     *
     * @param \XLite\Module\CDev\Coupons\Model\Coupon $coupon
     *
     * @return boolean
     */
    public function containsCoupon(\XLite\Module\CDev\Coupons\Model\Coupon $coupon)
    {
        return array_reduce($this->getUsedCoupons()->toArray(), function ($carry, $item) use ($coupon) {
            return $carry || $item->getCoupon()->getId() === $coupon->getId();
        }, false);
    }

    /**
     * Check if single use coupon present
     *
     * @return boolean
     */
    public function hasSingleUseCoupon()
    {
        return $this->getUsedCoupons()->exists(function ($key, $item) {
            return $item->getCoupon()->getSingleUse();
        });
    }

    // }}}

    // {{{ Status processors

    /**
     * Called when an order successfully placed by a client
     *
     * @return void
     */
    public function processSucceed()
    {
        parent::processSucceed();

        foreach ($this->getUsedCoupons() as $usedCoupons) {
            $usedCoupons->markAsUsed();
        }
    }

    /**
     * A "change status" handler
     *
     * @return void
     */
    protected function processUncheckout()
    {
        parent::processUncheckout();

        foreach ($this->getUsedCoupons() as $usedCoupons) {
            $usedCoupons->unmarkAsUsed();
        }
    }

    // }}}

    /**
     * Add usedCoupons
     *
     * @param \XLite\Module\CDev\Coupons\Model\UsedCoupon $usedCoupons
     * @return Order
     */
    public function addUsedCoupons(\XLite\Module\CDev\Coupons\Model\UsedCoupon $usedCoupons)
    {
        $this->usedCoupons[] = $usedCoupons;
        return $this;
    }

    /**
     * Get usedCoupons
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUsedCoupons()
    {
        return $this->usedCoupons;
    }

    /**
     * Get usedCoupons by coupon
     *
     * @param \XLite\Module\CDev\Coupons\Model\Coupon $coupon Coupon
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getValidItemsByCoupon(\XLite\Module\CDev\Coupons\Model\Coupon $coupon)
    {
        $items = array();

        foreach ($this->getItems() as $item) {
            if ($coupon->isValidForProduct($item->getProduct())) {
                $items[] = $item;
            }
        }

        return $items;
    }
}
