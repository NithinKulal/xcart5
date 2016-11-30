<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Cache;

/**
 * Trait ExecuteCachedTrait
 * @todo: add long lifetime cache
 * @package XLite\Core\Cache
 */
trait ExecuteCachedTrait
{
    /**
     * Callback will be executed only once unless it return null.
     * This cache is object ware.
     * The code below
     * ```php
     * protected $dataRuntimeCache;
     * public function getData()
     * {
     *     if (null === $this->dataRuntimeCache) {
     *         $this->dataRuntimeCache = $this->defineData();
     *     }
     *
     *     return $this->dataRuntimeCache;
     * }
     * ```
     * can be replaced with
     * ```php
     * use ExecuteCachedTrait;
     * public function getData()
     * {
     *     return $this->executeCachedRuntime([$this, 'defineData'], 'data-key');
     * }
     * ```
     * If $cacheKeyParts is omitted then function name will be used, 'getData' in this example.
     *
     * @todo: add additional callback params; use them in cacheKeyParts.
     * @param callable          $callback      Callback (the way to get initial value)
     * @param array|string|null $cacheKeyParts Cache cell name (it may be caller method name) OPTIONAL
     * @param boolean           $force         Force flag OPTIONAL
     *
     * @return mixed
     */
    protected function executeCachedRuntime(callable $callback, $cacheKeyParts = null, $force = false)
    {
        if (null === $cacheKeyParts) {
            $cacheKeyParts = debug_backtrace(\DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1]['function'];
        }

        return ExecuteCached::executeCachedRuntime($callback, $this->getRuntimeCacheKeyParts($cacheKeyParts), $force);
    }

    /**
     * Store object ware cache
     *
     * @param array|string $cacheKeyParts
     * @param mixed        $data
     */
    protected function setRuntimeCache($cacheKeyParts, $data)
    {
        ExecuteCached::setRuntimeCache($this->getRuntimeCacheKeyParts($cacheKeyParts), $data);
    }

    /**
     * Get object ware cache
     *
     * @param array|string $cacheKeyParts
     *
     * @return mixed|null
     */
    protected function getRuntimeCache($cacheKeyParts)
    {
        return ExecuteCached::getRuntimeCache($this->getRuntimeCacheKeyParts($cacheKeyParts));
    }

    /**
     * @param mixed $cacheKeyParts
     *
     * @return array
     */
    protected function getRuntimeCacheKeyParts($cacheKeyParts)
    {
        return [$cacheKeyParts, spl_object_hash($this), get_class($this)];
    }
}
