<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\EventListener;

/**
 * Abstract event listener 
 */
abstract class AEventListener extends \XLite\Base\Singleton
{
    /**
     * Errors 
     * 
     * @var array
     */
    protected $errors = array();

    /**
     * Arguments
     *
     * @var array
     */
    protected $arguments;

    /**
     * Handle event
     *
     * @param string $name      Event name
     * @param array  $arguments Event arguments OPTIONAL
     *
     * @return boolean
     */
    public static function handle($name, array $arguments = array())
    {
        return static::checkEvent($name, $arguments) ? static::getInstance()->handleEvent($name, $arguments) : false;
    }

    /**
     * Check event
     *
     * @param string $name      Event name
     * @param array  $arguments Event arguments OPTIONAL
     *
     * @return boolean
     */
    public static function checkEvent($name, array $arguments)
    {
        return true;
    }

    /**
     * Handle event (internal, after checking)
     *
     * @param string $name      Event name
     * @param array  $arguments Event arguments OPTIONAL
     *
     * @return boolean
     */
    public function handleEvent($name, array $arguments)
    {
        $this->arguments = $arguments;
    }

    /**
     * Get errors 
     * 
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

}

