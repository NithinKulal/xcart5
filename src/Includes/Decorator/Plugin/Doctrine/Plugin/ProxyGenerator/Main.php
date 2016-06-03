<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Includes\Decorator\Plugin\Doctrine\Plugin\ProxyGenerator;

/**
 * Main 
 *
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
        if (!$this->areProxiesExist()) {

            // Create the proxies folder
            \Includes\Utils\FileManager::mkdirRecursive(\Includes\Decorator\ADecorator::getCacheModelProxiesDir());

            // Create model proxy classes (second step of cache generation)
            \Includes\Decorator\Plugin\Doctrine\Utils\EntityManager::generateProxies();
        }
    }

    /**
     * Check if proxy classes are already generated
     *
     * @return boolean
     */
    protected function areProxiesExist()
    {
        return \Includes\Utils\FileManager::isDirReadable(\Includes\Decorator\ADecorator::getCacheModelProxiesDir());
    }
}
