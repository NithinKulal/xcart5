<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Includes\Decorator\Plugin\Doctrine\Plugin\Cache;

use XLite\Core\DependencyInjection\ContainerAwareTrait;

/**
 * Routines for Doctrine library
 *
 */
class Main extends \Includes\Decorator\Plugin\Doctrine\Plugin\APlugin
{
    use ContainerAwareTrait;

    // {{{ Hook handlers

    /**
     * Execute certain hook handler
     *
     * @return void
     */
    public function executeHookHandler()
    {
        $driver = \XLite\Core\Database::getInstance()->getCacheDriver();
        $driver->deleteAll();

        $this->getContainer()->get('widget_cache_manager')->deleteAll();
    }

    // }}}
}
