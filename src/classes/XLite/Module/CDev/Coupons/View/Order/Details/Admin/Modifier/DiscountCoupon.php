<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Coupons\View\Order\Details\Admin\Modifier;

/**
 * Discount coupon modifier widget
 */
class DiscountCoupon extends \XLite\View\Order\Details\Admin\Modifier
{
    /**
     * Get JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'modules/CDev/Coupons/order/page/parts/controller.js';

        return $list;
    }

    /**
     * Get CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/CDev/Coupons/order/page/parts/style.css';

        return $list;
    }

    /**
     * Return default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/CDev/Coupons/order/page/parts/totals.modifier.coupon.twig';
    }

    /**
     * Get used coupons
     *
     * @return array
     */
    protected function getUsedCoupons()
    {
        return $this->getOrder()->getUsedCoupons();
    }

    /**
     * Get coupon code hash
     *
     * @param \XLite\Module\CDev\Coupons\Model\UsedCoupon $coupon Used coupon entity
     *
     * @return string
     */
    protected function getCouponCodeHash($coupon)
    {
        return md5($coupon->getCode());
    }
}
