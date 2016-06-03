<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Includes\Decorator\Plugin\CleanupCache;

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
        return 'Cleaning up the cache...';
    }

    /**
     * Execute certain hook handler
     *
     * @return void
     */
    public function executeHookHandler()
    {
        // Remove old capsular directories
        if (\Includes\Decorator\Utils\CacheManager::isCapsular()) {
            $currentKey = \Includes\Decorator\Utils\CacheManager::getKey();
            foreach (\Includes\Decorator\Utils\CacheManager::getCacheDirs(true) as $dir) {
                $list = glob(rtrim($dir, LC_DS) . '.*');
                if ($list) {
                    foreach ($list as $subdir) {
                        list($main, $key) = explode('.', $subdir, 2);
                        if ($key && $key != $currentKey) {
                            \Includes\Utils\FileManager::unlinkRecursive($subdir);
                        }
                    }
                }
            }
        }

        \Includes\Decorator\Utils\CacheManager::cleanupCache();

        // Load classes from "classes" (do not use cache)
        \Includes\Autoloader::switchToOriginalClassDir();

        \Includes\Decorator\Plugin\Doctrine\Plugin\QuickData\Main::initializeCounter();
    }

}
