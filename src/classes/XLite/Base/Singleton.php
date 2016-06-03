<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Base;

/**
 * Singleton
 */
abstract class Singleton extends \XLite\Base\SuperClass
{
    /**
     * Array of instances for all derived classes
     *
     * @var array
     */
    protected static $instances = array();

    /**
     * Method to access a singleton
     *
     * @return static
     */
    public static function getInstance()
    {
        $className = get_called_class();

        // Create new instance of the object (if it is not already created)
        if (!isset(static::$instances[$className])) {
            static::$instances[$className] = new $className();
        }

        return static::$instances[$className];
    }

    /**
     * Destruct and recreate singleton
     *
     * @return \XLite\Base
     */
    public static function resetInstance()
    {
        $className = get_called_class();

        // Create new instance of the object (if it is not already created)
        if (isset(static::$instances[$className])) {
            unset(static::$instances[$className]);
        }

        return static::getInstance();
    }
}
