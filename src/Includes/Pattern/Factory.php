<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Includes\Pattern;

/**
 * Factory
 *
 */
abstract class Factory extends \Includes\Pattern\APattern
{
    /**
     * Class handlers cache
     *
     * @var array
     */
    protected static $classHandlers = array();

    /**
     * Create object instance and pass arguments to it contructor (if needed)
     *
     * @param string $class Class name
     * @param array  $args  Constructor arguments OPTIONAL
     *
     * @return object
     */
    public static function create($class, array $args = array())
    {
        $handler = static::getClassHandler($class);

        return $handler->hasMethod('__construct') ? $handler->newInstanceArgs($args) : $handler->newInstance();
    }

    /**
     * Return the Reflection handler for class
     *
     * @param string $class Class name
     *
     * @return \ReflectionClass
     */
    public static function getClassHandler($class)
    {
        if (!isset(static::$classHandlers[$class])) {
            static::$classHandlers[$class] = new \ReflectionClass($class);
        }

        return static::$classHandlers[$class];
    }
}
