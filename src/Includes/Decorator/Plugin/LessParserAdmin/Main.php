<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Includes\Decorator\Plugin\LessParserAdmin;

if (!defined('LC_CACHE_BUILDING_FINISH')) {
    define('LC_CACHE_BUILDING_FINISH', true);
}

/**
 * Admin LESS parser
 */
class Main extends \Includes\Decorator\Plugin\LessParser\Main
{
    /**
     * Execute certain hook handler
     *
     * @return void
     */
    public function executeHookHandler()
    {
        $adminLESS = static::getLESS(\XLite::ADMIN_INTERFACE);

        $lessParser = \XLite\Core\LessParser::getInstance();

        // Admin LESS files parsing
        $lessParser->setInterface('admin');

        $lessParser->setHttp('http');
        $lessParser->makeCSS($adminLESS);

        $lessParser = \XLite\Core\LessParser::resetInstance();

        $lessParser->setHttp('https');
        $lessParser->makeCSS($adminLESS);
    }
}
