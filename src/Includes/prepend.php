<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

// Temporary directories
define('LC_VAR_URL', 'var');

// Skins directories
define('LC_CUSTOMER_AREA_SKIN', LC_DIR_SKINS . 'default' . LC_DS . 'en' . LC_DS);
define('LC_ADMIN_AREA_SKIN', LC_DIR_SKINS . 'admin' . LC_DS . 'en' . LC_DS);

// Images subsystem settings
define('LC_IMAGES_URL', 'images');
define('LC_IMAGES_CACHE_URL', LC_VAR_URL . '/images');

// Files
define('LC_FILES_URL', 'files');

define('LC_HORN', 'Ly93d3cueC1jYXJ0LmNvbS9pbWc');

// OS
define('LC_OS_NAME', preg_replace('/^([^ ]+)/', '\\1', PHP_OS));
define('LC_OS_CODE', strtolower(substr(LC_OS_NAME, 0, 3)));
define('LC_OS_IS_WIN', LC_OS_CODE === 'win');

// Session type
define('LC_SESSION_TYPE', 'Sql');

set_include_path(
    LC_DIR_LIB
    . PATH_SEPARATOR . get_include_path()
);

// Some common functions
require_once (LC_DIR_ROOT . 'Includes' . LC_DS . 'functions.php');

// Common error reporting settings
$path = LC_DIR_LOG . 'php_errors.log.' . date('Y-m-d') . '.php';
if (!file_exists(dirname($path)) && is_writable(LC_DIR_VAR)) {
    \Includes\Utils\FileManager::mkdirRecursive(dirname($path));
}

if ((!file_exists($path) || 16 > filesize($path)) && is_writable(dirname($path))) {
    file_put_contents($path, '<' . '?php die(1); ?' . '>' . "\n");
    ini_set('error_log', $path);
}

ini_set('log_errors', true);
ini_set("auto_detect_line_endings", true);

unset($path);

// Set default memory limit
func_set_memory_limit('128M');
