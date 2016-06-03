<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Includes\Decorator\Plugin\FinishCacheBuildingHandler;

use XLite\Core\Database;
use XLite\Core\Translation;

if (!defined('LC_CACHE_BUILDING_FINISH')) {
    define('LC_CACHE_BUILDING_FINISH', true);
}

/**
 * Main
 */
class Main extends \Includes\Decorator\Plugin\APlugin
{
    /**
     * Execute certain hook handler
     *
     * @return void
     */
    public function executeHookHandler()
    {
        $translation = Translation::getInstance();

        $translation->reset();
        Database::getEM()->flush();

        $translation->resetDriver();
        $translation->translateByString('label');

        Database::getRepo('XLite\Model\TmpVar')->setVar(\XLite::CACHE_TIMESTAMP, intval(microtime(true)));
    }
}
