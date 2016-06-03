<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Includes\Decorator\Plugin\Decorate;

/**
 * Main 
 */
class Main extends \Includes\Decorator\Plugin\APlugin
{

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return 'Decorate classes...';
    }

    /**
     * Execute certain hook handler
     *
     * @return void
     */
    public function executeHookHandler()
    {
        \Includes\Decorator\Utils\CacheManager::getClassesTree()->walkThrough(
            array('\Includes\Decorator\Utils\Operator', 'decorateClass')
        );
    }

}
