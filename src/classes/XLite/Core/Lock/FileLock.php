<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Lock;

define('LC_DIR_LOCKS', LC_DIR_DATACACHE . LC_DS . 'locks' . LC_DS);
/**
 * File lock
 */
class FileLock extends \XLite\Base\Singleton implements \XLite\Core\Lock\ILock
{
    /**
     * Lock files directory
     */
    const LOCK_DIR      = LC_DIR_LOCKS;
    const FILE_SUFFIX   = '.lock';

    /**
     * Default time to live in seconds (one day)
     */
    const DEFAULT_TTL = 86400;

    /**
     * Constructor
     * Creates directory for locks if needed
     */
    public function __construct()
    {
        if (!\Includes\Utils\FileManager::isExists(rtrim(static::LOCK_DIR, LC_DS))) {
            \Includes\Utils\FileManager::mkdirRecursive(rtrim(static::LOCK_DIR, LC_DS));
        }
        if (
            !\Includes\Utils\FileManager::isReadable(static::LOCK_DIR)
            || !\Includes\Utils\FileManager::isWriteable(static::LOCK_DIR)
        ) {
            \XLite\Logger::getInstance()->log(
                'Cannot create lock for keys',
                LOG_DEBUG
            );
        }
        parent::__construct();
    }

    /**
     * Get filename by provided key
     * 
     * @param string    $key    Lock identifier
     * 
     * @return string Filename
     */
    protected function getFileName($key)
    {
        return static::LOCK_DIR . $key . static::FILE_SUFFIX;
    }

    /**
     * Check if provided key should be removed
     * 
     * @param string    $filename   Filename of lock
     * @param integer   $ttl        Time to live in seconds
     */
    protected function isExpired($filename, $ttl)
    {
        clearstatcache();
        $lastModified = @filemtime($filename);

        $expirationTime = file_get_contents($filename);

        if ( empty($expirationTime) ) {
            $realTtl = $ttl ?: static::DEFAULT_TTL;
            $expirationTime = $lastModified + $realTtl;
        }

        return null !== $lastModified
            && time() > $expirationTime;
    }

    /**
     * Check if provided key is not released
     * 
     * @param string    $key        Lock identifier
     * @param boolean   $strict     Do not check for expiration
     * @param integer   $ttl        Time to live in seconds OPTIONAL
     * 
     * @return boolean
     */
    public function isRunning($key, $strict = false, $ttl = null) {
        $result = false;
        $filename = $this->getFileName($key);
        if (
            file_exists($filename)
            && ($strict || !$this->isExpired($filename, $ttl))
        ) {
            $result = true;
        } else {
            $this->release($key);
        }

        return $result;
    }

    /**
     * Mark provided key as running
     * Puts time of key expiring
     * 
     * @param string    $key    Lock identifier
     * @param integer   $ttl    Time to live in seconds OPTIONAL
     * 
     * @return void
     */
    public function setRunning($key, $ttl = null) {
        $content = null === $ttl
            ? ''
            : time() + $ttl;
        file_put_contents(
            $this->getFileName($key),
            $content
        );
    }

    /**
     * Mark provided key as released
     * 
     * @param string $key Lock identifier
     * 
     * @return void
     */
    public function release($key) {
        $filename = $this->getFileName($key);
        if (file_exists($filename)) {
            unlink($this->getFileName($key));
        }
    }
}
