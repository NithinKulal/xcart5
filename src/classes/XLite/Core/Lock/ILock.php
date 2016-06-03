<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Lock;

/**
 * Lock
 */
interface ILock
{
    /**
     * Check if provided key is not released
     * 
     * @param string    $key        Lock identifier
     * @param boolean   $strict     Do not check for expiration
     * @param integer   $ttl        Time to live in seconds
     * 
     * @return boolean
     */
    public function isRunning($key, $strict, $ttl);

    /**
     * Mark provided key as running
     * Puts time of key expiring
     * 
     * @param string    $key    Lock identifier
     * @param integer   $ttl    Time to live in seconds
     * 
     * @return void
     */
    public function setRunning($key, $ttl);

    /**
     * Mark provided key as released
     * 
     * @param string $key Lock identifier
     * 
     * @return void
     */
    public function release($key);
}
