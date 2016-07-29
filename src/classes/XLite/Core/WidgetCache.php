<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core;

/**
 * Widget cache represents a specific widget's cache setup
 */
class WidgetCache
{
    /**
     * Widget cache prefix
     */
    const CACHE_PREFIX      = 'viewCache.';

    /**
     * Widget cache namespace
     */
    const CACHE_NAMESPACE   = 'viewCacheNamespace';

    /**
     * @var WidgetCacheRegistryInterface
     */
    private $cacheRegistry;

    /**
     * Cache driver instance
     *
     * @var \Doctrine\Common\Cache\CacheProvider
     */
    protected $cacheDriver;

    /**
     * Protected constructor.
     * It's not possible to instantiate a derived class (using the "new" operator)
     * until that child class is not implemented public constructor
     *
     * @return void
     */
    protected function getCacheDriver()
    {
        if (!$this->cacheDriver) {
            $this->cacheDriver = \XLite\Core\Database::getFreshCacheDriver();
            $this->cacheDriver->setNamespace(static::CACHE_NAMESPACE);
        }

        return $this->cacheDriver;
    }

    /**
     * Delete by partial key
     *
     * @param array $parameters Cache cell keys
     *
     * @return void
     */
    public function delete(array $parameters)
    {
        $key = $this->getCacheKey($parameters);
        $this->getCacheDriver()->delete($key);
    }

    /**
     * Delete all
     *
     * @return boolean
     */
    public function deleteAll()
    {
        return $this->getCacheDriver()->deleteAll();
    }

    /**
     * Check cache
     *
     * @param array $parameters Cache cell keys
     *
     * @return string
     */
    public function has(array $parameters)
    {
        return $this->getCacheDriver()->contains($this->getCacheKey($parameters));
    }

    /**
     * Get cache
     *
     * @param array $parameters Cache cell keys
     *
     * @return mixed
     */
    public function get(array $parameters)
    {
        $content = $this->getCacheDriver()->fetch($this->getCacheKey($parameters));

        return $content !== false ? unserialize($content) : null;
    }

    /**
     * Set cache
     *
     * @param array   $parameters Cache cell keys
     * @param mixed   $content    Serializable content
     * @param integer $ttl        TTL (seconds) OPTIONAL
     *
     * @return void
     */
    public function set(array $parameters, $content, $ttl = null)
    {
        $key = $this->getCacheKey($parameters);
        $ttl = $ttl ?: \XLite\Model\Repo\ARepo::CACHE_DEFAULT_TTL;

        $this->getCacheDriver()->save($key, serialize($content), $ttl);
    }

    /**
     * Remove cache
     *
     * @param array $parameters Cache cell keys
     *
     * @return string
     */
    public function remove(array $parameters)
    {
        $key = $this->getCacheKey($parameters);
        $this->getCacheDriver()->delete($key);
    }

    /**
     * Get cache key
     *
     * @param array $parameters Parameters OPTIONAL
     *
     * @return string
     */
    protected function getCacheKey(array $parameters = array())
    {
        return md5(static::CACHE_PREFIX . implode('.', $parameters));
    }
}
