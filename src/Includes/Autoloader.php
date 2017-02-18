<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Includes;

use Includes\Decorator\ADecorator;
use Includes\Decorator\Utils\Operator;

/**
 * Autoloader
 *
 */
abstract class Autoloader
{
    /**
     * The directory where LC classes are located
     *
     * @var string
     */
    protected static $lcAutoloadDir = LC_DIR_CACHE_CLASSES;

    /** @var \Includes\Autoload\ClassAutoLoaderInterface */
    protected static $classCacheAutoloader;

    /**
     * Main autoloader checking procedure.
     * Work for the inner XC classes only
     *
     * @param string $class name of the class to load
     *
     * @return bool
     */
    public static function checkAutoload($class)
    {
        /**
         * NOTE: it's the PHP bug: in some cases it adds or removes the leading slash. Examples:
         *
         * 1. For static call "\Includes\Decorator\Utils\CacheManager::rebuildCache()" it will remove
         * the leading slash, and class name passed in this function will be "Includes\Decorator\Utils\CacheManager".
         *
         * 2. Pass class name as a string into the functions, e.g.
         * "is_subclass_of($object, '\Includes\Decorator\Utils\CacheManager')". Then the class
         * name will be passed into the autoloader with the leading slash - "\Includes\Decorator\Utils\CacheManager"
         *
         * Remove the "ltrim()" call when this issue will be resolved
         *
         * May be that issue is related: http://bugs.php.net/50731
         */
        $class = ltrim($class, '\\');
        list($prefix) = explode('\\', $class, 2);
        $result = false;

        if ($prefix === LC_NAMESPACE || $prefix === LC_NAMESPACE . 'Abstract') {
            $result = file_exists(static::$lcAutoloadDir . str_replace('\\', LC_DS, $class) . '.php');
        }

        return $result;
    }

    /**
     * Main LC autoloader
     *
     * @param string $class name of the class to load
     *
     * @return void
     */
    public static function __lc_autoload($class)
    {
        /**
         * NOTE: it's the PHP bug: in some cases it adds or removes the leading slash. Examples:
         *
         * 1. For static call "\Includes\Decorator\Utils\CacheManager::rebuildCache()" it will remove
         * the leading slash, and class name passed in this function will be "Includes\Decorator\Utils\CacheManager".
         *
         * 2. Pass class name as a string into the functions, e.g.
         * "is_subclass_of($object, '\Includes\Decorator\Utils\CacheManager')". Then the class
         * name will be passed into the autoloader with the leading slash - "\Includes\Decorator\Utils\CacheManager"
         *
         * Remove the "ltrim()" call when this issue will be resolved
         *
         * May be that issue is related: http://bugs.php.net/50731
         */
        $class = ltrim($class, '\\');
        list($prefix) = explode('\\', $class, 2);

        if ($prefix === LC_NAMESPACE || $prefix === LC_NAMESPACE . 'Abstract') {
            include_once (static::$lcAutoloadDir . str_replace('\\', LC_DS, $class) . '.php');
        }
    }

    /**
     * Autoloader for the "includes"
     *
     * @param string $class name of the class to load
     *
     * @return void
     */
    public static function __lc_autoload_includes($class)
    {
        $class = ltrim($class, '\\');

        if (0 === strpos($class, LC_NAMESPACE_INCLUDES)) {
            include_once (LC_DIR_ROOT . str_replace('\\', LC_DS, $class) . '.php');
        }
    }

    /**
     * Register generated autoloader for PSR-4 compliant classes
     *
     * @param  string $namespace Root namespace
     * @param  string $path      Absolute path to folder, where classes are placed
     */
    public static function registerCustom($namespace, $path)
    {
        spl_autoload_register(
            function ($class) use ($namespace, $path) {
                $class = ltrim($class, '\\');

                if (0 === strpos($class, $namespace)) {
                    include_once ($path . '//../' . str_replace('\\', LC_DS, $class) . '.php');
                }
            }
        );
    }

    protected static function registerClassDir()
    {
        spl_autoload_register(array(get_called_class(), '__lc_autoload'));
    }

    public static function registerClassCacheProductionAutoloader()
    {
        self::unregisterClassCacheAutoloader();

        self::$classCacheAutoloader = new Autoload\ClassAutoLoader(Operator::getCacheClassesDir());

        self::$classCacheAutoloader->register();
    }

    public static function registerClassCacheDevelopmentAutoloader()
    {
        self::unregisterClassCacheAutoloader();

        // First, trying to get module list from DB
        $activeModules = array_keys(
            array_filter(Utils\ModulesManager::fetchModulesListFromDB(), function ($module) {
                return $module['enabled'];
            })
        );

        // If module list is empty (we're on cache rebuild), then call ModulesManager::getActiveModules
        if (empty($activeModules)) {
            // Autoload switch trick is required because ModulesManager::getActiveModules accesses some classes from classes/
            self::registerClassCacheProductionAutoloader();

            $activeModules = array_keys(Utils\ModulesManager::getActiveModules());

            self::unregisterClassCacheAutoloader();
        }

        self::$classCacheAutoloader = new Autoload\DevClassAutoLoader(LC_DIR_CLASSES, Operator::getCacheClassesDir(), $activeModules);

        self::$classCacheAutoloader->register();
    }

    /**
     * Reinitialize autoloader if needed
     * For example, after module uninstalled
     * More convenient way could be dynamic dependency
     * of Autoload\DevClassAutoLoader on modules list, but KISS
     *
     * @return void
     */
    public static function reinitializeIfNeeded()
    {
        if (LC_DEVELOPER_MODE) {
            static::registerClassCacheDevelopmentAutoloader();
        }
    }

    public static function unregisterClassCacheAutoloader()
    {
        if (self::$classCacheAutoloader !== null) {
            self::$classCacheAutoloader->unregister();
        }
    }

    /**
     * Register autoload functions
     *
     * @return void
     */
    public static function registerEverythingExceptClassCache()
    {
        spl_autoload_register(array(get_called_class(), '__lc_autoload_includes'));

        // Initialize X-Cart classes directory
        static::initializeClassesDir();

        // PEAR2
        static::registerPEARAutolader();

        // Load lessphp
        static::loadLESSPhp();

        //// Load PHPMailer
        //static::loadPHPMailer();

        // Register Doctrine proxy autoloader
        \Doctrine\Common\Proxy\Autoloader::register(
            rtrim(\Includes\Decorator\ADecorator::getCacheModelProxiesDir(), LC_DS),
            LC_MODEL_PROXY_NS
        );
    }

    /**
     * Autoloader for PEAR2
     *
     * @return void
     */
    protected static function registerPEARAutolader()
    {
        require_once (LC_DIR_LIB . 'PEAR2' . LC_DS . 'Autoload.php');
    }

    /**
     * Load lessphp
     *
     * @return void
     */
    protected static function loadLESSPhp()
    {
        require_once (LC_DIR_LIB . 'Less' . LC_DS . 'Less.php');
    }

    /**
     * Switch autoload directory from var/run/classes/ to classes/
     *
     * @return void
     */
    public static function switchToOriginalClassDir()
    {
        static::$lcAutoloadDir = LC_DIR_CLASSES;

        self::unregisterClassCacheAutoloader();

        self::registerClassDir();
    }

    /**
     * Return path ot the autoloader current dir
     *
     * @return string
     */
    public static function getLCAutoloadDir()
    {
        return static::$lcAutoloadDir;
    }

    /**
     * Initialize classes directory
     * 
     * @return void
     */
    protected static function initializeClassesDir()
    {
        static::$lcAutoloadDir = \Includes\Decorator\ADecorator::getCacheClassesDir();
    }
}
