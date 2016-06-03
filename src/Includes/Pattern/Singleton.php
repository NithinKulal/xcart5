<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Includes\Pattern;

/**
 * Singleton
 *
 * @package XLite
 */
abstract class Singleton extends \Includes\Pattern\APattern
{
    /**
     * Class instances
     *
     * @var array
     */
    protected static $instances = array();


    /**
     * Protected constructur
     *
     * @return void
     */
    protected function __construct()
    {
    }


    /**
     * Return object instance
     *
     * @return static
     */
    public static function getInstance()
    {
        if (!isset(static::$instances[$class = get_called_class()])) {
            static::$instances[$class] = new static();
        }

        return static::$instances[$class];
    }
}
