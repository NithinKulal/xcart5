<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */


/**
 * Initialization of X-Cart installation
 */

if (!defined('XLITE_INSTALL_MODE')) {
    die('Incorrect call of the script. Stopping.');
}

if (version_compare(phpversion(), '5.4.0') >= 0) {
    error_reporting(E_ALL ^ E_DEPRECATED);

} else {
    die('X-Cart cannot start on PHP version earlier than 5.4.0 (' . phpversion(). ' is currently used)');
}

ini_set('display_errors', true);
ini_set('display_startup_errors', true);

@set_time_limit(300);

umask(0);

require_once __DIR__ . '/../../top.inc.php';

require_once constant('LC_DIR_ROOT') . 'Includes/install/install_settings.php';

// suphp mode
define('LC_SUPHP_MODE', get_php_execution_mode());
