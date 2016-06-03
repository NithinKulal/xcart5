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
    const CACHE_PREFIX = 'viewCache.';

    /**
     * @var WidgetCacheRegistryInterface
     */
    private $cacheRegistry;

    public function __construct(WidgetCacheRegistryInterface $cacheRegistry)
    {
        $this->cacheRegistry = $cacheRegistry;
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
        $registry = $this->cacheRegistry->fetchRegistry();

        foreach ($this->cacheRegistry->getRegistryKey($parameters) as $key) {
            Database::getCacheDriver()->delete($key);
            unset($registry[$key]);
        }

        $this->cacheRegistry->saveRegistry($registry);
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
        return Database::getCacheDriver()->contains($this->getCacheKey($parameters));
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
        $content = Database::getCacheDriver()->fetch($this->getCacheKey($parameters));

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

        $this->cacheRegistry->setRegistry($key, $parameters);

        \XLite\Core\Database::getCacheDriver()->save($key, serialize($content), $ttl);
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

        Database::getCacheDriver()->delete($key);
        $this->cacheRegistry->unsetRegistry($key);
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
