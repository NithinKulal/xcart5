<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

define('LC_ERR_TAG_MSG',   '@MSG@');
define('LC_ERR_TAG_ERROR', '@ERROR@');
define('LC_ERR_TAG_CODE',  '@CODE@');

define('LC_ERROR_PAGE_MESSAGE', 'ERROR: "' . LC_ERR_TAG_ERROR . '" (' . LC_ERR_TAG_CODE . ') - ' . LC_ERR_TAG_MSG . LC_EOL);

/**
 * Display error message
 *
 * @param string  $code    Error code
 * @param string  $message Error message
 * @param string  $page    Template of message to display
 *
 * @return void
 */
function showErrorPage($code, $message, $page = LC_ERROR_PAGE_MESSAGE, $prefix = 'ERROR_', $http_code = 500)
{
    header('Content-Type: text/html; charset=utf-8', true, $http_code);

    echo str_replace(
        array(LC_ERR_TAG_MSG, LC_ERR_TAG_ERROR, LC_ERR_TAG_CODE),
        array($message, str_replace($prefix, '', $code), defined($code) ? constant($code) : 'N/A'),
        $page
    );

    exit (intval($code) ? $code : 1);
}

// Check PHP version before any other operations
if (!defined('LC_DO_NOT_CHECK_PHP_VERSION') && version_compare(PHP_VERSION, '5.3.0', '<')) {
    showErrorPage('ERROR_UNSUPPORTED_PHP_VERSION', 'Min allowed PHP version is 5.3.0');
}
