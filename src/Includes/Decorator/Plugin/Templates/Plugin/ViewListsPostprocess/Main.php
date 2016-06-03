<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Includes\Decorator\Plugin\Templates\Plugin\ViewListsPostprocess;

use Includes\Decorator\Utils\CacheManager;
use XLite\Core\Database;
use XLite\Model\ViewList;

/**
 * Main
 */
class Main extends \Includes\Decorator\Plugin\Templates\Plugin\APlugin
{

    /**
     * Check - current plugin is bocking or not
     *
     * @return boolean
     */
    public function isBlockingPlugin()
    {
        return CacheManager::isCapsular();
    }

    /**
     * Execute certain hook handler
     *
     * @return void
     */
    public function executeHookHandler()
    {
        // Delete old and rename new
        if (CacheManager::isCapsular()) {
            ViewList::setVersionKey(null);

            $repo = Database::getRepo('XLite\Model\ViewList');
            $key  = CacheManager::getKey();

            $repo->deleteObsolete($key);
            $repo->markCurrentVersion($key);
        }
    }
}
