<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core;

/**
 * Cache decorator
 */
class Cache extends \XLite\Base
{
    /**
     * Cache driver
     *
     * @var \Doctrine\Common\Cache\Cache
     */
    protected $driver;

    /**
     * Options 
     * 
     * @var array
     */
    protected $options;

    /**
     * Cache drivers query
     *
     * @var array
     */
    protected static $cacheDriversQuery = array(
        'apc',
        'xcache',
        'memcached',
        'memcache',
    );

    /**
     * Constructor
     *
     * @param \Doctrine\Common\Cache\Cache $driver  Driver OPTIONAL
     * @param array                        $options Driver options OPTIONAL
     *
     * @return void
     */
    public function __construct(\Doctrine\Common\Cache\Cache $driver = null, array $options = array())
    {
        $this->options = $options;
        $this->driver = $driver ?: $this->detectDriver();
    }

    /**
     * Get driver 
     * 
     * @return \Doctrine\Common\Cache\Cache
     */
    public function getDriver()
    {
        return $this->driver;
    }

    /**
     * Call driver's method
     *
     * @param string $name      Method name
     * @param array  $arguments Arguments OPTIONAL
     *
     * @return mixed
     */
    public function __call($name, array $arguments = array())
    {
        return call_user_func_array(array($this->driver, $name), $arguments);
    }

    /**
     * Detect APC cache driver
     *
     * @return boolean
     */
    protected static function detectCacheDriverApc()
    {
        return function_exists('apc_cache_info');
    }

    /**
     * Detect XCache cache driver
     *
     * @return boolean
     */
    protected static function detectCacheDriverXcache()
    {
        return function_exists('xcache_get');
    }

    /**
     * Detect Memcache cache driver
     *
     * @return boolean
     */
    protected static function detectCacheDriverMemcache()
    {
        return function_exists('memcache_connect');
    }

    /**
     * Detect Memcache cache driver
     *
     * @return boolean
     */
    protected static function detectCacheDriverMemcached()
    {
        return extension_loaded('memcached');
    }

    /**
     * Get cache driver by options list
     *
     * @return \Doctrine\Common\Cache\Cache
     */
    protected function detectDriver()
    {
        $options = \XLite::getInstance()->getOptions('cache');

        if (empty($options) || !is_array($options) || !isset($options['type'])) {
            $options = array('type' => null);
        }

        $this->options += $options;

        // Auto-detection
        if ('auto' == $this->options['type']) {
            $this->detectAutoDriver();
        }

        if ('apc' == $this->options['type']) {

            // APC
            $cache = $this->buildAPCDriver();

        } elseif ('memcached' == $this->options['type']
            && isset($this->options['servers'])
            && static::detectCacheDriverMemcached()
            && class_exists('Memcached', false)
        ) {

            // Memcached
            $cache = $this->buildMemcachedDriver();

        } elseif ('memcache' == $this->options['type']
            && isset($this->options['servers'])
            && static::detectCacheDriverMemcache()
            && class_exists('Memcache', false)
        ) {

            // Memcache
            $cache = $this->buildMemcacheDriver();

        } elseif ('xcache' == $this->options['type']) {

            // XCache
            $cache = $this->buildXcacheDriver();

        } else {

            // Default cache - file system cache
            $cache = $this->buildFileDriver();

        }

        if (!$cache) {
            $cache = new \Doctrine\Common\Cache\ArrayCache();
        }

        $namespace = $this->getNamespace();
        if (!empty($namespace)) {
            $cache->setNamespace($namespace);
        }

        return $cache;
    }

    /**
     * Autodetect driver 
     * 
     * @return void
     */
    protected function detectAutoDriver()
    {
        foreach (static::$cacheDriversQuery as $type) {
            $method = 'detectCacheDriver' . ucfirst($type);

            // $method assembled from 'detectCacheDriver' + $type
            if (static::$method()) {
                $this->options['type'] = $type;
                break;
            }
        }
    }

    /**
     * Get namespace 
     * 
     * @return string
     */
    protected function getNamespace()
    {
        $namespace = empty($this->options['namespace'])
            ? ''
            : ($this->options['namespace'] . '_');

        if (isset($this->options['original'])) {
            $namespace .= \Includes\Decorator\Utils\CacheManager::getDataCacheSuffix($this->options['original']);

        } else {
            $namespace .= \Includes\Decorator\Utils\CacheManager::getDataCacheSuffix();
        }

        return $namespace;
    }

    // {{{ Builders

    /**
     * Build APC driver 
     * 
     * @return  \Doctrine\Common\Cache\CacheProvider
     */
    protected function buildAPCDriver()
    {
        return new \Doctrine\Common\Cache\ApcCache;
    }

    /**
     * Build Memcache driver
     *
     * @return  \Doctrine\Common\Cache\CacheProvider
     */
    protected function buildMemcacheDriver()
    {
        $servers = explode(';', $this->options['servers']) ?: array('localhost');
        $memcache = new \Memcache();
        foreach ($servers as $row) {
            $row = trim($row);
            $tmp = explode(':', $row, 2);
            if ('unix' == $tmp[0]) {
                $memcache->addServer($row, 0);

            } elseif (isset($tmp[1])) {
                $memcache->addServer($tmp[0], $tmp[1]);

            } else {
                $memcache->addServer($tmp[0]);
            }
        }

        $cache = new \Doctrine\Common\Cache\MemcacheCache;
        $cache->setMemcache($memcache);

        return $cache;
    }

    /**
     * Build Memcache driver
     *
     * @return  \Doctrine\Common\Cache\CacheProvider
     */
    protected function buildMemcachedDriver()
    {
        $servers = explode(';', $this->options['servers']) ?: array('localhost');
        $memcached = new \Memcached();
        foreach ($servers as $row) {
            $row = trim($row);
            $tmp = explode(':', $row, 2);
            if ('unix' == $tmp[0]) {
                $memcached->addServer($row, 0);

            } elseif (isset($tmp[1])) {
                $memcached->addServer($tmp[0], $tmp[1]);

            } else {
                $memcached->addServer($tmp[0]);
            }
        }

        $cache = new \Doctrine\Common\Cache\MemcachedCache;
        $cache->setMemcached($memcached);

        return $cache;
    }

    /**
     * Build Xcache driver
     *
     * @return  \Doctrine\Common\Cache\CacheProvider
     */
    protected function buildXcacheDriver()
    {
        return new \Doctrine\Common\Cache\XcacheCache;
    }

    /**
     * Build filesystem cache driver
     *
     * @return  \Doctrine\Common\Cache\CacheProvider
     */
    protected function buildFileDriver()
    {
        $cache = new \XLite\Core\FileCache(LC_DIR_DATACACHE);

        return $cache->isValid() ? $cache : null;
    }
    // }}}

}
