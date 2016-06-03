<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . 'top.inc.php');

set_time_limit(0);

XLite::getInstance()->run();

\XLite\Core\Probe::getInstance()->measure();
