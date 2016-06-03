<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Lock;

/**
 * Object locker (in data cache)
 */
abstract class AObjectCacheLocker extends \XLite\Base\Singleton implements \XLite\Core\Lock\ILock
{
    /**
     * Default TTL (1 hour)
     */
    const TTL = 3600;

    /**
     * Default TTL to wait until lock released (in seconds)
     *
     * @var integer
     */
    const TTL_WAIT = 10;

    /**
     * Runtime cache of data
     *
     * @var array
     */
    protected $data = array();

    /**
     * Cache driver
     *
     * @var \Doctrine\Common\Cache\CacheProvider
     */
    protected $cacheDriver = null;

    /**
     * Get object identifier
     *
     * @param mixed $object Object
     *
     * @return string
     */
    abstract protected function getIdentifier($object);

    /**
     * Fetch locked info
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->fetch();
    }

    /**
     * Lock object
     *
     * @param mixed        $object Object
     * @param integer|null $ttl    TTL OPTIONAL
     *
     * @return void
     */
    public function lock($object, $ttl = null)
    {
        $this->setRunning($this->getIdentifier($object), $this->getTTL($ttl));
    }

    /**
     * Unlock object
     *
     * @param mixed $object Object
     *
     * @return void
     */
    public function unlock($object)
    {
        $this->release($this->getIdentifier($object));
    }

    /**
     * Check locked state
     *
     * @param mixed        $object Object
     * @param boolean      $strict Do not check for expiration OPTIONAL
     * @param integer|null $ttl    Time to live in seconds OPTIONAL
     *
     * @return boolean
     */
    public function isLocked($object, $strict = false, $ttl = null)
    {
        return $this->isRunning($this->getIdentifier($object), $strict, $this->getTTL($ttl));
    }

    /**
     * Wait until lock has released
     * Return true if object is unlocked and false otherwise
     *
     * @param mixed        $object Object
     * @param integer      $limit  TTL to wait for lock release (in seconds)
     * @param boolean      $strict Do not check for expiration OPTIONAL
     * @param integer|null $ttl    Time to live in seconds OPTIONAL
     *
     * @return boolean
     */
    public function waitForUnlocked($object, $limit = null, $strict = false, $ttl = null)
    {
        $result = false;

        if (is_null($limit)) {
            $limit = static::TTL_WAIT;
        }

        while (
            0 <= $limit
            && $result = $this->isRunning($this->getIdentifier($object), $strict, $this->getTTL($ttl))
        ) {
            $limit --;
            sleep(1);
        }

        return !$result;
    }

    /**
     * Set lock on object
     *
     * @param string  $key Object identifier
     * @param integer $ttl TTL
     *
     * @return void
     */
    public function setRunning($key, $ttl)
    {
        $this->data[$key] = \LC_START_TIME + $ttl;
        $this->save();
    }

    /**
     * Release lock on object
     *
     * @param string $key Object identifier
     *
     * @return void
     */
    public function release($key)
    {
        unset($this->data[$key]);
        $this->save();
    }

    /**
     * Return true if object is locked
     *
     * @param mixed   $key    Object identifier
     * @param boolean $strict Do not check for expiration
     * @param integer $ttl    Time to live in seconds
     *
     * @return boolean
     */
    public function isRunning($key, $strict, $ttl)
    {
        return isset($this->data[$key]) && ($strict || !$this->isExpired($this->data[$key]));
    }

    /**
     * Get lock TTL
     *
     * @param mixed|null $ttl
     *
     * @return integer
     */
    protected function getTTL($ttl = null)
    {
        return null === $ttl ? static::TTL : (int) $ttl;
    }

    /**
     * Return true if lock has expired
     *
     * @param mixed $time Time to check
     *
     * @return boolean
     */
    protected function isExpired($time)
    {
        return (int) $time < \LC_START_TIME;
    }

    /**
     * Get object key to save lock in database
     *
     * @return string
     */
    protected function getKey()
    {
        return get_class($this);
    }

    /**
     * Fetch data from cache
     *
     * @return void
     */
    protected function fetch()
    {
        $data = $this->getCacheDriver() ? $this->getCacheDriver()->fetch($this->getKey()) : null;
        $this->data = is_array($data) ? $data : array();
    }

    /**
     * Save data to cache
     *
     * @return void
     */
    protected function save()
    {
        if ($this->getCacheDriver()) {
            $this->getCacheDriver()->save($this->getKey(), $this->data);
        }
    }

    /**
     * Get cache driver
     *
     * @return \Doctrine\Common\Cache\CacheProvider
     */
    protected function getCacheDriver()
    {
        if (!isset($this->cacheDriver)) {
            $this->cacheDriver = \XLite\Core\Database::getCacheDriver();
            if (!$this->cacheDriver) {
                $this->cacheDriver = false;
            }
        }

        return $this->cacheDriver;
    }
}
