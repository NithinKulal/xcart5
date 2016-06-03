<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Includes\Decorator\Plugin\Doctrine\Plugin\PostprocessFixtures;

use Includes\Decorator\Plugin\Doctrine\Utils\FixturesManager;
use Includes\Decorator\Utils\CacheManager;

/**
 * Main 
 */
class Main extends \Includes\Decorator\Plugin\Doctrine\Plugin\APlugin
{
    /**
     * Execute certain hook handle
     *
     * @return void
     */
    public function executeHookHandler()
    {
        // Postprocess step (Load fixtures)
        if (FixturesManager::getFixtures()) {
            CacheManager::$skipStepCompletion = true;

        } else {
            FixturesManager::removeFixtures();
        }
    }
}
