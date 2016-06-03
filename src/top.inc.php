<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

// No PHP warnings are allowed in LC
error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', true);

// Short name
define('LC_DS', DIRECTORY_SEPARATOR);

// Modes
define('LC_IS_CLI_MODE', 'cli' === PHP_SAPI);

// Common end-of-line
define('LC_EOL', LC_IS_CLI_MODE ? "\n" : '<br />');

require_once (__DIR__ . LC_DS . 'vendor' . LC_DS . 'autoload.php');

// Define error handling functions and check PHP version (if needed)
require_once (__DIR__ . LC_DS . 'error_handler.php');
require_once (__DIR__ . LC_DS . 'top.inc.PHP53.php');

if (defined('LC_INCLUDE_ADDITIONAL')) {
    // Clean URLs support
    define('LC_USE_CLEAN_URLS', \XLite\Core\Config::getInstance()->CleanURL->clean_url_flag , true);
}
