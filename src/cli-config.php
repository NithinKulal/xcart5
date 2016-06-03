<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

/*
 * Doctrine Console configuration
 */

if ('cli' != PHP_SAPI) {
    exit (1);
}

use Doctrine\ORM\Tools\Console\ConsoleRunner;
use XLite\Core\Database;

// Bootstrap X-Cart:
define('LC_INCLUDE_ADDITIONAL', true);
require_once (__DIR__ . DIRECTORY_SEPARATOR . 'top.inc.php');

$entityManager = Database::getEM();

return ConsoleRunner::createHelperSet($entityManager);
