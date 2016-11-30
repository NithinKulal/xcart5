<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Cache;

/**
 * Class ExecuteCached
 * @todo    : add long lifetime cache
 * @package XLite\Core\Cache
 */
class ExecuteCached
{
    protected static $runtimeCacheStorage = [];

    /**
     * Callback will be executed only once unless it return null.
     * This cache is application ware.
     * The code below
     * ```php
     * protected static $dataRuntimeCache;
     * public function getData()
     * {
     *     if (null === static::$dataRuntimeCache) {
     *         static::$dataRuntimeCache = $this->defineData();
     *     }
     *
     *     return static::$dataRuntimeCache;
     * }
     * ```
     * can be replaced with
     * ```php
     * public function getData()
     * {
     *     return ExecuteCached::executeCachedRuntime([$this, 'defineData'], 'data-key');
     * }
     * ```
     *
     * @todo: add additional callback params; use them in cacheKeyParts.
     *
     * @param callable     $callback      Callback (the way to get initial value)
     * @param array|string $cacheKeyParts Cache cell name (it may be caller method name)
     * @param boolean      $force         Force flag OPTIONAL
     *
     * @return mixed
     */
    public static function executeCachedRuntime(callable $callback, $cacheKeyParts, $force = false)
    {
        $cacheKey = static::getRuntimeCacheKey($cacheKeyParts);

        if (!isset(static::$runtimeCacheStorage[$cacheKey]) || $force) {
            static::$runtimeCacheStorage[$cacheKey] = $callback();
        }

        return static::$runtimeCacheStorage[$cacheKey];
    }

    /**
     * Store application ware cache
     *
     * @param array|string $cacheKeyParts
     * @param mixed        $data
     */
    public static function setRuntimeCache($cacheKeyParts, $data)
    {
        $cacheKeyParts = static::getRuntimeCacheKey($cacheKeyParts);

        static::$runtimeCacheStorage[$cacheKeyParts] = $data;
    }

    /**
     * Get application ware cache
     *
     * @param array|string $cacheKeyParts
     *
     * @return mixed|null
     */
    public static function getRuntimeCache($cacheKeyParts)
    {
        $cacheKeyParts = static::getRuntimeCacheKey($cacheKeyParts);

        return isset(static::$runtimeCacheStorage[$cacheKeyParts])
            ? static::$runtimeCacheStorage[$cacheKeyParts]
            : null;
    }

    /**
     * Calculate key for cache storage
     *
     * @param mixed $cacheKeyParts
     *
     * @return string
     */
    public static function getRuntimeCacheKey($cacheKeyParts)
    {
        return is_scalar($cacheKeyParts) ? (string) $cacheKeyParts : md5(serialize($cacheKeyParts));
    }

    /**
     * @param callable $callback
     * @param mixed    $cacheKeyParts
     * @param int      $lifeTime
     * @param bool     $force
     *
     * @return mixed
     */
    public static function executeCached(callable $callback, $cacheKeyParts, $lifeTime = 0, $force = false)
    {
        $driver   = \XLite\Core\Cache::getInstance()->getDriver();
        $cacheKey = static::getCacheKey($cacheKeyParts);

        if ($driver->contains($cacheKey)) {
            return $driver->fetch($cacheKey);
        }

        $result = $callback();
        $driver->save($cacheKey, $result, $lifeTime);

        return $result;
    }

    /**
     * Store application ware cache
     *
     * @param array|string $cacheKeyParts
     * @param mixed        $data
     * @param int          $lifeTime
     */
    public static function setCache($cacheKeyParts, $data, $lifeTime = 0)
    {
        $driver   = \XLite\Core\Cache::getInstance()->getDriver();
        $cacheKey = static::getCacheKey($cacheKeyParts);

        $driver->save($cacheKey, $data, $lifeTime);
    }

    /**
     * Get application ware cache
     *
     * @param array|string $cacheKeyParts
     *
     * @return mixed|null
     */
    public static function getCache($cacheKeyParts)
    {
        $driver   = \XLite\Core\Cache::getInstance()->getDriver();
        $cacheKey = static::getCacheKey($cacheKeyParts);

        return $driver->fetch($cacheKey);
    }

    /**
     * @param mixed $cacheKeyParts
     *
     * @return string
     */
    public static function getCacheKey($cacheKeyParts)
    {
        return is_scalar($cacheKeyParts) ? (string) $cacheKeyParts : md5(serialize($cacheKeyParts));
    }
}
