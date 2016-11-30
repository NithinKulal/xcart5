<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MailChimp\Logic\DataMapper;


class Order
{
    /**
     * Get order data
     *
     * @param                    $mc_cid
     * @param                    $mc_eid
     * @param \XLite\Model\Order $order
     *
     * @return array
     */
    public static function getDataByOrder($mc_cid, $mc_eid, $mc_tc, \XLite\Model\Order $order, $customerExists)
    {
        \XLite\Core\Translation::setTmpTranslationCode(
            \XLite\Core\Config::getInstance()->General->default_language
        );

        $customerData = [];

        if ($order->getProfile()) {
            $customerData = !$customerExists
                ? Customer::getData($mc_eid, $order->getProfile())
                : [ 'id' => strval($order->getProfile()->getProfileId()) ];
        } else {
            $customerData = [ 'id' => strval($mc_eid) ];
        }

        $return = array(
            'id'                    => strval($order->getOrderNumber()),
            'customer'              => $customerData,
            'financial_status'      => $order->getPaymentStatus()->getName(),
            'fulfillment_status'    => $order->getShippingStatus()->getName(),
            'currency_code'         => $order->getCurrency()->getCode(),
            'order_total'           => $order->getTotal(),
            'tax_total'             => static::getTaxValue($order),
            'shipping_total'        => $order->getSurchargeSumByType(\XLite\Model\Base\Surcharge::TYPE_SHIPPING),
            'order_date'            => $order->getDate(),
            'processed_at_foreign'  => date('c', $order->getDate()),
            'updated_at_foreign'    => date('c', $order->getLastRenewDate()),
            'lines'                 => static::getLines($order),
        );
        
        if ($mc_tc) {
            $return['tracking_code'] = strval($mc_tc);
        }
        
        if ($mc_cid) {
            $return['campaign_id'] = $mc_cid;
        }
        
        if ($order->getProfile()) {
            if ($order->getProfile()->getShippingAddress()) {
                $return['shipping_address'] = Address::getData(
                    $order->getProfile()->getShippingAddress()
                );
            }

            if ($order->getProfile()->getBillingAddress()) {
                $return['billing_address'] = Address::getData(
                    $order->getProfile()->getBillingAddress()
                );
            }
        }

        \XLite\Core\Translation::setTmpTranslationCode(null);

        return $return;
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

    /**
     * Get lines data
     * 
     * @param \XLite\Model\Order $order
     *
     * @return array
     */
    protected static function getLines(\XLite\Model\Order $order)
    {
        $lines = array();

        /** @var \XLite\Model\OrderItem $item */
        foreach ($order->getItems() as $item) {
            $lines[] = Line::getDataByOrderItem($item);
        }

        return $lines;
    }
}