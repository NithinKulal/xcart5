<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\GoogleAnalytics\Module\CDev\Coupons\DataMapper;

/**
 * Class OrderDataMapper
 *
 * @Decorator\Depend ("CDev\Coupons")
 */
class OrderDataMapper extends \XLite\Module\CDev\GoogleAnalytics\Logic\DataMapper\OrderDataMapper implements \XLite\Base\IDecorator
{
    /**
     * @param \XLite\Model\Order $order
     *
     * @return array
     */
    public static function getPurchaseData(\XLite\Model\Order $order)
    {
        $result = parent::getPurchaseData($order);

        $coupons = $order->getUsedCoupons()->map(function($coupon) {
            return $coupon->getPublicCode();
        })->toArray();

        if ($coupons) {
            $result['coupon'] = join(', ', $coupons);
        }

        return $result;
    }
}