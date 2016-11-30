<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\GoogleAnalytics\Logic\DataMapper;

/**
 * Class OrderDataMapper
 */
class OrderDataMapper
{
    /**
     * Get purchase order data
     *
     * @param \XLite\Model\Order $order
     *
     * @return array
     */
    public static function getPurchaseData(\XLite\Model\Order $order)
    {
        $tax        = static::getTaxValue($order);
        $shipping   = $order->getSurchargeSumByType(\XLite\Model\Base\Surcharge::TYPE_SHIPPING);

        return [
            'id'            => $order->getOrderNumber(),
            'affiliation'   => \XLite\Core\Config::getInstance()->Company->company_name,
            'revenue'       => strval($order->getTotal()),
            'tax'           => strval($tax),
            'shipping'      => strval($shipping),
            'coupon'        => '',
        ];
    }

    /**
     * @param \XLite\Model\Order $order
     *
     * @return array
     */
    public static function getPurchaseDataForBackend(\XLite\Model\Order $order)
    {
        $data = static::getPurchaseData($order);

        $result = [];
        $result['ti'] = $data['id'];
        $result['ta'] = $data['affiliation'];
        $result['tr'] = $data['revenue'];
        $result['tt'] = $data['tax'];
        $result['ts'] = $data['shipping'];
        $result['tcc'] = $data['coupon'];

        return $result;
    }

    /**
     * @param \XLite\Model\Order $order
     * @param array              $change
     *
     * @return array
     */
    public static function getChangeDataForBackend(\XLite\Model\Order $order, array $change)
    {
        $data = static::getPurchaseData($order);

        $change = array_merge(
            [
                'revenue'   => '0',
                'tax'       => '0',
                'shipping'  => '0',
            ],
            array_filter($change)
        );

        $result = [];
        $result['ti'] = $data['id'];
        $result['ta'] = $data['affiliation'];
        $result['tr'] = $change['revenue'];
        $result['tt'] = $change['tax'];
        $result['ts'] = $change['shipping'];
        $result['tcc'] = $data['coupon'];

        return $result;
    }

    /**
     * Get tax value
     *
     * @param \XLite\Model\Order $order
     *
     * @return float
     */
    protected static function getTaxValue(\XLite\Model\Order $order)
    {
        $total = 0;

        /** @var \XLite\Model\Order\Surcharge $s */
        foreach ($order->getSurchargesByType(\XLite\Model\Base\Surcharge::TYPE_TAX) as $s) {
            $total += $s->getValue();
        }

        return $total;
    }
}