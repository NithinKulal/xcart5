<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Includes\Decorator\Utils;

/**
 * Cache information driver
 */
abstract class PersistentInfo
{
    /**
     * Name of data file
     */
    const FILE_NAME = '.persistentInfo';

    /**
     * Data (local cache)
     * 
     * @var   array
     */
    protected static $data = array();

    /**
     * Get data cell
     * 
     * @param string $name Cell name
     *  
     * @return mixed
     */
    public static function get($name)
    {
        $data = static::getData();

        return isset($data[$name]) ? $data[$name] : null;
    }

    /**
     * Set data cell
     * 
     * @param string $name  Cell name
     * @param mixed  $value Value
     *  
     * @return void
     */
    public static function set($name, $value)
    {
        $data = static::getData();

        $data[$name] = $value;

        static::setData($data);
    }

    /**
     * Remove cache info file
     * 
     * @return void
     */
    public static function remove()
    {
        $path = static::getFilename();

        if (file_exists($path)) {
            unlink($path);
        }
    }

    /**
     * Get file path to data storage file
     *
     * @return string
     */
    public static function getFilename()
    {
        return LC_DIR_VAR . static::FILE_NAME;
    }

    /**
     * Get data 
     *
     * @return array
     */
    protected static function getData()
    {
        if (empty(static::$data)) {
            $path = static::getFilename();
            if (file_exists($path) && is_readable($path)) {
                static::$data = file_get_contents($path);
                static::$data = empty(static::$data) ? array() : @unserialize(static::$data);
            }

            if (!is_array(static::$data)) {
                static::$data = array();
            }
        }

        return static::$data;
    }

    /**
     * Set data 
     * 
     * @param array $data Data
     *  
     * @return void
     */
    protected static function setData(array $data)
    {
        $path = static::getFilename();

        static::$data = $data;

        if (false === @file_put_contents($path, serialize(static::$data))) {
            \Includes\ErrorHandler::fireError(
                'Unable write to "' . $path . '" file. Please correct the permissions'
            );
        }
    }

    /**
     * Unset data cell. If data contains no cells after this operation, file is removed.
     * 
     * @param string $name  Cell name
     *  
     * @return void
     */
    public static function discard($name)
    {
        $data = static::getData();

        unset($data[$name]);

        if (count($data) > 0) {
            static::setData($data);
        } else {
            static::remove();
        }
    }
}
