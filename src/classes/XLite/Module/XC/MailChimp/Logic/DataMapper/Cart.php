<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MailChimp\Logic\DataMapper;

use XLite\Module\XC\MailChimp\Core\MailChimpECommerce;

class Cart
{
    /**
     * Get order data
     *
     * @param                    $mc_cid
     * @param                    $mc_eid
     * @param \XLite\Model\Cart $order
     *
     * @return array
     */
    public static function getDataByCart($mc_cid, $mc_eid, $mc_tc, \XLite\Model\Cart $object, $customerExists)
    {
        \XLite\Core\Translation::setTmpTranslationCode(
            \XLite\Core\Config::getInstance()->General->default_language
        );

        $customerData = [];

        if ($object->getProfile()) {
            $customerData = !$customerExists
                ? Customer::getData($mc_eid, $object->getProfile())
                : [ 'id' => strval($object->getProfile()->getProfileId()) ];
        } else {
            $customerData = [ 'id' => strval($mc_eid) ];
        }

        $return = array(
            'id'                    => strval($object->getOrderId()),
            'customer'              => $customerData,
            'campaign_id'           => $mc_cid,
            'checkout_url'          => \XLite::getInstance()->getShopURL(
                \XLite\Core\Converter::buildURL('cart'),
                \XLite\Core\Config::getInstance()->Security->customer_security
            ),
            'currency_code'         => $object->getCurrency()->getCode(),
            'order_total'           => $object->getTotal(),
            'tax_total'             => static::getTaxValue($object),
            'shipping_total'        => $object->getSurchargeSumByType(\XLite\Model\Base\Surcharge::TYPE_SHIPPING),
            'lines'                 => static::getLines($object),
        );
        
        if ($mc_tc) {
            $return['tracking_code'] = strval($mc_tc);
        }
        
        if ($object->getProfile()) {
            if ($object->getProfile()->getShippingAddress()) {
                $return['shipping_address'] = Address::getData(
                    $object->getProfile()->getShippingAddress()
                );
            }

            if ($object->getProfile()->getBillingAddress()) {
                $return['billing_address'] = Address::getData(
                    $object->getProfile()->getBillingAddress()
                );
            }
        }

        \XLite\Core\Translation::setTmpTranslationCode(null);

        return $return;
    }

    /**
     * Get tax value
     *
     * @param \XLite\Model\Cart $object
     *
     * @return float
     */
    protected static function getTaxValue(\XLite\Model\Cart $object)
    {
        $total = 0;

        /** @var \XLite\Model\Order\Surcharge $s */
        foreach ($object->getSurchargesByType(\XLite\Model\Base\Surcharge::TYPE_TAX) as $s) {
            $total += $s->getValue();
        }

        return $total;
    }

    /**
     * Get lines data
     *
     * @param \XLite\Model\Cart $object
     *
     * @return array
     */
    protected static function getLines(\XLite\Model\Cart $object)
    {
        $lines = array();

        /** @var \XLite\Model\OrderItem $item */
        foreach ($object->getItems() as $item) {
            $lines[] = Line::getDataByOrderItem($item);
        }

        return $lines;
    }
}