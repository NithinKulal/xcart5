<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Includes\Decorator\Plugin\PHPCache\Plugin\OPcache;

/**
 * Main 
 *
 */
class Main extends \Includes\Decorator\Plugin\PHPCache\Plugin\APlugin
{
    /**
     * Name of the function to clean cache
     */
    const CLEAR_FUNCTION = 'opcache_reset';

    /**
     * Execute certain hook handle
     *
     * @return void
     */
    public function executeHookHandler()
    {
        if (function_exists(static::CLEAR_FUNCTION)) {
            call_user_func(static::CLEAR_FUNCTION);
        }
    }
}
