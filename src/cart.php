<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

// One minute to execute the script
@set_time_limit(300);

try {
    define('LC_INCLUDE_ADDITIONAL', true);
    require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . 'top.inc.php');

    \XLite::getInstance()->runCustomerZone();
    \XLite\Logger::getInstance()->executePostponedLogs();

} catch (\Exception $e) {
    \Includes\ErrorHandler::handleException($e);
}
