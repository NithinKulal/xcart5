<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Stripe\Model\Payment;

/**
 * Payment transaction
 */
class Transaction extends \XLite\Model\Payment\Transaction implements \XLite\Base\IDecorator
{
    const IPN_TTL_PREFIX = 'lock_ttl_';

    /**
     * Check if transaction by payment method
     *
     * @return boolean
     */
    public function isCallbackLockExpired()
    {
        return $this->hasCallbackLock() && (int) $this->getIpnTtl() < \LC_START_TIME;
    }

    /**
     * Check if transaction has active IPN ttl
     *
     * @return boolean
     */
    public function hasCallbackLock()
    {
        return ((int) $this->getIpnTtl()) > 0;
    }

    /**
     * Set a lock for IPN request (in case payment return wouldnt happen).
     * When lock TTL will expire, IPNs for transaction are processed regardless of payment return.
     * @param int $ttl_time Lock TTL
     */
    public function lockCallbackProcessing($ttl_time = 3600)
    {
        $timestamp = \LC_START_TIME + $ttl_time;
        $this->setIpnTtl($timestamp);
    }

    /**
     * Removes the lock on IPN requests
     */
    public function unlockCallbackProcessing()
    {
        $this->removeTtlForIpn();
    }

    /**
     * Remove TTL for IPN requests
     */
    protected function removeTtlForIpn()
    {
        \XLite\Core\Database::getCacheDriver()->delete($this->getIpnTtlCacheKey());
    }

    /**
     * Returns TTL for IPN request expire timestamp
     *
     * @return integer
     */
    protected function getIpnTtl()
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
}
