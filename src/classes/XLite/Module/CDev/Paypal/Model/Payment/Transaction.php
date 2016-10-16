<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\Model\Payment;

use \XLite\Module\CDev\Paypal;

/**
 * Payment transaction
 */
class Transaction extends \XLite\Model\Payment\Transaction implements \XLite\Base\IDecorator
{
    const IPN_TTL_PREFIX = 'cdev_paypal_ipn_ttl_';

    /**
     * Returns TTL for IPN request expire timestamp
     *
     * @return integer
     */
    public function getIpnTtl()
    {   
        return \XLite\Core\Database::getCacheDriver()->fetch($this->getIpnTtlCacheKey()) ?: 0;
    }

    /**
     * Sets TTL for IPN request expire timestamp
     */
    protected function setIpnTtl($ttl)
    {   
        \XLite\Core\Database::getCacheDriver()->save($this->getIpnTtlCacheKey(), $ttl);
    }

    /**
     * Returns TTL for IPN cache key
     * 
     * @return string
     */
    protected function getIpnTtlCacheKey()
    {
        return static::IPN_TTL_PREFIX . $this->getUniqueIdentifier();
    }

    /**
     * Check if transaction by Paypal payment method
     *
     * @return boolean
     */
    public function isByPayPal()
    {
        /** @var \XLite\Model\Payment\Method $paymentMethod */
        $paymentMethod = $this->getPaymentMethod();
        return in_array($paymentMethod->getServiceName(), Paypal\Main::getServiceCodes(), true);
    }

    /**
     * Check if transaction by Paypal payment method
     *
     * @return boolean
     */
    public function isTtlExpired()
    {   
        return $this->hasTtlForIpn() && (int) $this->getIpnTtl() < \LC_START_TIME;
    }

    /**
     * Check if transaction has active IPN ttl
     *
     * @return boolean
     */
    public function hasTtlForIpn()
    {
        return ((int) $this->getIpnTtl()) > 0;
    }

    /**
     * Remove TTL for IPN requests
     *
     * @return boolean
     */
    public function removeTtlForIpn()
    {
        \XLite\Core\Database::getCacheDriver()->delete($this->getIpnTtlCacheKey());
    }

    /**
     * Set a TTL for IPN request (in case payment return wouldnt happen). 
     * When TTL will expire, IPNs for transaction are processed regardless of payment return.
     * Set to 0 to disable TTL
     */
    public function setTtlForIpn($ttl_time = 3600)
    {
        $timestamp = \LC_START_TIME + $ttl_time;
        $this->setIpnTtl($timestamp);
    }
}
