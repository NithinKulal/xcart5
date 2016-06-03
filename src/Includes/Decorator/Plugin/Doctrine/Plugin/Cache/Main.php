<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Includes\Decorator\Plugin\Doctrine\Plugin\Cache;

/**
 * Routines for Doctrine library
 *
 */
class Main extends \Includes\Decorator\Plugin\Doctrine\Plugin\APlugin
{
    // {{{ Hook handlers

    /**
     * Execute certain hook handler
     *
     * @return void
     */
    public function executeHookHandler()
    {
        $driver = new \XLite\Core\Cache();
        $driver->deleteAll();
    }

    // }}}
}
