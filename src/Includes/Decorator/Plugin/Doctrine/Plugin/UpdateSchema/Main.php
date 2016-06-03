<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Includes\Decorator\Plugin\Doctrine\Plugin\UpdateSchema;

use Includes\Decorator\Plugin\Doctrine\Utils\SchemaMigrationManager;
use XLite\Core\Database;

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
        Database::getInstance()->executeQueries(
            SchemaMigrationManager::getMigration()->getQueries()
        );
    }
}