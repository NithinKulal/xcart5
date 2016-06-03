<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */


use Includes\Autoloader;
use Includes\Decorator\Utils\CacheManager;

// It's the feature of PHP 5. We need to explicitly define current time zone.
// See also http://bugs.php.net/bug.php?id=48914
@date_default_timezone_set(@date_default_timezone_get());

// Timestamp of the application start
define('LC_START_TIME', time());

// Namespaces
define('LC_NAMESPACE',          'XLite');
define('LC_NAMESPACE_INCLUDES', 'Includes');
define('LC_MODEL_NS',           LC_NAMESPACE . '\Model');
define('LC_MODEL_PROXY_NS',     LC_MODEL_NS . '\Proxy');

// Paths
define('LC_DIR',                 realpath(__DIR__));
define('LC_DIR_ROOT',            rtrim(LC_DIR, LC_DS) . LC_DS);
define('LC_DIR_CLASSES',         LC_DIR_ROOT . 'classes' . LC_DS);
define('LC_DIR_VAR',             LC_DIR_ROOT . 'var' . LC_DS);
define('LC_DIR_LIB',             LC_DIR_ROOT . 'lib' . LC_DS);
define('LC_DIR_SKINS',           LC_DIR_ROOT . 'skins' . LC_DS);
define('LC_DIR_IMAGES',          LC_DIR_ROOT . 'images' . LC_DS);
define('LC_DIR_FILES',           LC_DIR_ROOT . 'files' . LC_DS);
define('LC_DIR_CONFIG',          LC_DIR_ROOT . 'etc' . LC_DS);
define('LC_DIR_INCLUDES',        LC_DIR_ROOT . LC_NAMESPACE_INCLUDES . LC_DS);
define('LC_DIR_MODULES',         LC_DIR_CLASSES . LC_NAMESPACE . LC_DS . 'Module' . LC_DS);
define('LC_DIR_COMPILE',         LC_DIR_VAR . 'run' . LC_DS);
define('LC_DIR_CACHE_CLASSES',   LC_DIR_COMPILE . 'classes' . LC_DS);
define('LC_DIR_CACHE_SKINS',     LC_DIR_COMPILE . 'skins' . LC_DS);
define('LC_DIR_CACHE_MODULES',   LC_DIR_CACHE_CLASSES . LC_NAMESPACE . LC_DS . 'Module' . LC_DS);
define('LC_DIR_CACHE_MODEL',     LC_DIR_CACHE_CLASSES . LC_NAMESPACE . LC_DS . 'Model' . LC_DS);
define('LC_DIR_CACHE_PROXY',     LC_DIR_CACHE_MODEL . 'Proxy' . LC_DS);
define('LC_DIR_CACHE_RESOURCES', LC_DIR_VAR . 'resources' . LC_DS);
define('LC_DIR_BACKUP',          LC_DIR_VAR . 'backup' . LC_DS);
define('LC_DIR_DATA',            LC_DIR_VAR . 'data' . LC_DS);
define('LC_DIR_TMP',             LC_DIR_VAR . 'tmp' . LC_DS);
define('LC_DIR_LOCALE',          LC_DIR_VAR . 'locale');
define('LC_DIR_DATACACHE',       LC_DIR_VAR . 'datacache');
define('LC_DIR_LOG',             LC_DIR_VAR . 'log' . LC_DS);
define('LC_DIR_CACHE_IMAGES',    LC_DIR_VAR . 'images' . LC_DS);
define('LC_DIR_SERVICE',         LC_DIR_FILES . 'service' . LC_DS);

define('LC_OS_WINDOWS', 'WIN' === strtoupper(substr(PHP_OS, 0, 3)));

// Disabled xdebug coverage for Selenium-based tests [DEVELOPMENT PURPOSE]
if (isset($_COOKIE) && !empty($_COOKIE['no_xdebug_coverage']) && function_exists('xdebug_stop_code_coverage')) {
    @xdebug_stop_code_coverage();
}

// Autoloading routines
require_once (LC_DIR_INCLUDES . 'Autoloader.php');
Autoloader::registerEverythingExceptClassCache();

// Fire the error if LC is not installed
if (!defined('XLITE_INSTALL_MODE')) {
    \Includes\ErrorHandler::checkIsLCInstalled();
}

// So called "developer" mode. Set it to "false" for production mode!
define('LC_DEVELOPER_MODE', (bool) \Includes\Utils\ConfigParser::getOptions(array('performance', 'developer_mode')));
define('LC_CACHE_NAMESPACE_HASH', (bool) \Includes\Utils\ConfigParser::getOptions(array('performance', 'cache_namespace_hash')));

// Correct error handling mode
ini_set('display_errors', LC_DEVELOPER_MODE);

// Fatal error and exception handlers
register_shutdown_function(array('\Includes\ErrorHandler', 'shutdown'));
set_exception_handler(array('\Includes\ErrorHandler', 'handleException'));

@umask(0000);

require_once (LC_DIR_INCLUDES . 'prepend.php');

Autoloader::registerClassCacheProductionAutoloader();

// Check and (if needed) rebuild classes cache
if (!defined('LC_DO_NOT_REBUILD_CACHE')) {
    CacheManager::rebuildCache();
}

// Do not register development class cache autoloader when:
// 1) Cache rebuild is in progress (other process is rebuilding the cache in separate var/run folder).
// 2) Script has opted out of using development class cache autoloader by defining LC_DO_NOT_REBUILD_CACHE (for example, ./restoredb)
if (LC_DEVELOPER_MODE && !CacheManager::isRebuildInProgress() && !defined('LC_DO_NOT_REBUILD_CACHE')) {
    Autoloader::registerClassCacheDevelopmentAutoloader();
} else {
    Autoloader::registerClassCacheProductionAutoloader();
}
// Safe mode
if (!defined('XLITE_INSTALL_MODE')) {
    \Includes\SafeMode::initialize();
}
