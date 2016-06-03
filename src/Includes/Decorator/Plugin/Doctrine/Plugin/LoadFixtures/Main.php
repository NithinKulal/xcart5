<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Includes\Decorator\Plugin\Doctrine\Plugin\LoadFixtures;

use Includes\Decorator\Plugin\Doctrine\Utils\FixturesManager;
use Includes\Decorator\Utils\CacheManager;
use Includes\Utils\Database as UtilsDatabase;
use Includes\Utils\Operator;
use XLite\Core\Database;

/**
 * Main
 *
 */
class Main extends \Includes\Decorator\Plugin\Doctrine\Plugin\APlugin
{
    const STEP_TTL = 10;

    /**
     * Execute certain hook handle
     *
     * @return void
     */
    public function executeHookHandler()
    {
        $list = FixturesManager::getFixtures();

        if ($list) {
            Operator::showMessage('', true, false);

            foreach ($list as $fixture) {
                $message = '...Load ' . substr($fixture, strlen(LC_DIR_ROOT));
                Operator::showMessage($message, true, true);
                CacheManager::logMessage(PHP_EOL);
                CacheManager::logMessage($message);

                if (static::isYAML($fixture)) {
                    // Load YAML fixture
                    Database::getInstance()->loadFixturesFromYaml($fixture);

                } else {
                    // Load SQL queries
                    UtilsDatabase::uploadSQLFromFile($fixture);
                }

                FixturesManager::removeFixtureFromList($fixture);

                if (CacheManager::isTimeExceeds(static::STEP_TTL)) {
                    break;
                }
            }

        }

        CacheManager::logMessage(PHP_EOL);

        Database::getEM()->clear();
    }

    /**
     * Check if the file contains .yaml extension
     *
     * @param string $file
     *
     * @return boolean
     */
    protected static function isYAML($file)
    {
        return '.yaml' === substr($file, -5);
    }
}
