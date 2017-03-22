#!/usr/bin/env php
<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

if ('cli' != PHP_SAPI) {
    exit (1);
}

define('LC_INCLUDE_ADDITIONAL', true);
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . 'top.inc.php');

\XLite::getInstance()->run(true)->getViewer()->display();
\XLite\Logger::getInstance()->executePostponedLogs();

echo PHP_EOL;

exit (defined('CLI_RESULT_CODE') ? CLI_RESULT_CODE : 0);
