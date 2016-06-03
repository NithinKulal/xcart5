<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\EventDriver;

/**
 * Abstract event driver 
 */
abstract class AEventDriver extends \XLite\Base
{
    /**
     * Fire event
     * 
     * @param string $name      Event name
     * @param array  $arguments Arguments OPTIONAL
     *  
     * @return boolean
     */
    abstract public function fire($name, array $arguments = array());

    /**
     * Check driver
     * 
     * @return boolean
     */
    public static function isValid()
    {
        return true;
    }

    /**
     * Get driver code 
     * 
     * @return string
     */
    public static function getCode()
    {
        return null;
    }

    /**
     * Constructor
     * 
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Current driver is blocking
     * 
     * @return boolean
     */
    public function isBlocking()
    {
        return false;
    }
}
