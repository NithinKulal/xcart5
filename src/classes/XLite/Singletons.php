<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite;

/**
 * Singletons
 */
class Singletons
{
    /**
     * handler
     *
     * @var \Includes\Singletons
     */
    public static $handler;

    /**
     * classNames
     *
     * @var array
     */
    protected static $classNames = array(
        'xlite'   => '\XLite',
        'request' => '\XLite\Core\Request',
        'layout'  => '\XLite\Core\Layout',
        'session' => '\XLite\Core\Session',
        'config'  => '\XLite\Core\Config',
        'auth'    => '\XLite\Core\Auth',
    );

    /**
     * __constructStatic
     *
     * @return void
     */
    public static function __constructStatic()
    {
        static::$handler = new static();
    }

    /**
     * Magic getter
     *
     * @param string $name Variable name
     *
     * @return \XLite\Base\Singleton
     */
    public function __get($name)
    {
        $this->$name = call_user_func(array(static::$classNames[$name], 'getInstance'));

        return $this->$name;
    }
}

// Call static constructor
\XLite\Singletons::__constructStatic();
