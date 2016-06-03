<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Coupons\Controller\Admin;

/**
 * Order page controller
 */
class Order extends \XLite\Controller\Admin\Order implements \XLite\Base\IDecorator
{
    /**
     * Define the actions with no secure token
     *
     * @return array
     */
    public static function defineFreeFormIdActions()
    {
        return array_merge(parent::defineFreeFormIdActions(), array('check_coupon'));
    }

    /**
     * Check coupon
     *
     * @return void
     */
    protected function doActionCheckCoupon()
    {
        $code = (string) \XLite\Core\Request::getInstance()->code;
        /** @var \XLite\Module\CDev\Coupons\Model\Coupon $coupon */
        $coupon = \XLite\Core\Database::getRepo('XLite\Module\CDev\Coupons\Model\Coupon')
            ->findOneByCode($code);

        $order = $this->getOrder();

        if ($coupon) {
            $error = $this->checkCompatibility($coupon, $order);
        } else {
            $error = static::t(
                'There is no such a coupon, please check the spelling: X',
                array('code' => $code)
            );
        }

        $data = array(
            'error' => null,
        );

        if ($error) {
            $data['error'] = $error;

        } else {
            $data['amount'] = $coupon->getAmount($order);
            $data['publicName'] = $coupon->getPublicName();
        }

        $this->setPureAction();

        $this->suppressOutput = true;
        $this->silent = true;

        print json_encode($data);
    }

    /**
     * @param \XLite\Module\CDev\Coupons\Model\Coupon $coupon Coupon
     * @param \XLite\Model\Order                      $order  Order
     *
     * @return string
     */
    protected function checkCompatibility($coupon, $order)
    {
        $error = '';

        try {
            $coupon->checkCompatibility($order) && $coupon->checkUnique($order);
        } catch (\XLite\Module\CDev\Coupons\Core\CompatibilityException $exception) {
            $error = static::t($exception->getMessage(), $exception->getParams());
        }

        return $error;
    }

    /**
     * Assemble coupon discount dump surcharge
     *
     * @return array
     */
    protected function assembleDcouponDumpSurcharge()
    {
        return $this->assembleDefaultDumpSurcharge(
            \XLite\Model\Base\Surcharge::TYPE_DISCOUNT,
            \XLite\Module\CDev\Coupons\Logic\Order\Modifier\Discount::MODIFIER_CODE,
            '\XLite\Module\CDev\Coupons\Logic\Order\Modifier\Discount',
            static::t('Coupon discount')
        );
    }

    /**
     * Get required surcharges
     *
     * @return array
     */
    protected function getRequiredSurcharges()
    {
        $result = parent::getRequiredSurcharges();

        $cnd = new \XLite\Core\CommonCell();
        $couponsCount = \XLite\Core\Database::getRepo('XLite\Module\CDev\Coupons\Model\Coupon')->search($cnd, true);

        if (0 < $couponsCount) {
            $result = array_merge(
                $result,
                array(\XLite\Module\CDev\Coupons\Logic\Order\Modifier\Discount::MODIFIER_CODE)
            );
        }

        return $result;
    }

    /**
     * Add human readable name for DCOUPON modifier code
     *
     * @return array
     */
    protected static function getFieldHumanReadableNames()
    {
        return array_merge(
            parent::getFieldHumanReadableNames(),
            array(
                \XLite\Module\CDev\Coupons\Logic\Order\Modifier\Discount::MODIFIER_CODE  => 'Coupon discount',
            )
        );
    }

    /**
     * Update order items list
     *
     * @param \XLite\Model\Order $order Order object
     *
     * @return void
     */
    protected function updateOrderItems($order)
    {
        parent::updateOrderItems($order);
        $this->processCoupons($order);
    }

    /**
     * Process coupons
     *
     * @param \XLite\Model\Order $order Order
     *
     * @return void
     */
    protected function processCoupons(\XLite\Model\Order $order)
    {
        $request = \XLite\Core\Request::getInstance();

        // Remove coupon
        foreach ($order->getUsedCoupons() as $coupon) {
            $hash = md5($coupon->getCode());
            if ($coupon->getCode() && !empty($request->removeCoupons[$hash])) {
                // Register order change
                static::setOrderChanges(
                    'Removed coupons:' . $coupon->getId(),
                    $coupon->getCode()
                );
                // Remove used coupon from order
                $order->getUsedCoupons()->removeElement($coupon);
                \XLite\Core\Database::getEM()->remove($coupon);
            }
        }

        // Add coupon
        if (!empty($request->newCoupon) && is_array($request->newCoupon)) {
            foreach ($request->newCoupon as $code) {
                if ($code) {
                    $coupon = \XLite\Core\Database::getRepo('XLite\Module\CDev\Coupons\Model\Coupon')
                        ->findOneByCode($code);

                    if ($coupon) {
                        $usedCoupon = new \XLite\Module\CDev\Coupons\Model\UsedCoupon;
                        $usedCoupon->setOrder($order);
                        $order->addUsedCoupons($usedCoupon);
                        $usedCoupon->setCoupon($coupon);
                        $coupon->addUsedCoupons($usedCoupon);
                        \XLite\Core\Database::getEM()->persist($usedCoupon);

                        // Register order change
                        static::setOrderChanges(
                            'Added coupons:' . $coupon->getId(),
                            $coupon->getCode()
                        );
                    }
                }
            }
        }
    }

    /**
     * Assemble recalculate order event: Add coupons data
     *
     * @param \XLite\Model\Order $order Order
     *
     * @return array
     */
    protected function assembleRecalculateOrderEvent(\XLite\Model\Order $order)
    {
        $result = parent::assembleRecalculateOrderEvent($order);

        $coupons = array();

        foreach ($order->getUsedCoupons() as $coupon) {
            $coupons[$coupon->getCode()] = abs($coupon->getValue());
        }

        if ($coupons) {
            $result['coupons'] = $coupons;
        }

        return $result;
    }
}
