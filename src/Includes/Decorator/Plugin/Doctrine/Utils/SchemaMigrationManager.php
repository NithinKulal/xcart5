<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Includes\Decorator\Plugin\Doctrine\Utils;

use Includes\Database\Migration\Migration;
use Includes\Database\Migration\MigrationType;
use Includes\Decorator\Utils\CacheManager;
use Includes\Utils\FileManager;
use XLite\Core\Database;

/**
 * DbSchemaMigrationManager stores Migration object associated with a current rebuild process
 */
abstract class SchemaMigrationManager extends \Includes\Decorator\Plugin\Doctrine\ADoctrine
{
    /**
     * Cached Migration object
     *
     * @var Migration
     */
    protected static $migration;

    /**
     * Get stored migration
     *
     * @return Migration
     */
    public static function getMigration()
    {
        if (!isset(self::$migration)) {
            $path = self::getMigrationFilePath();

            if (FileManager::isFileReadable($path)) {
                $content = FileManager::read($path);

                if ($content) {
                    self::$migration = unserialize($content);
                }
            }
        }

        return self::$migration;
    }

    /**
     * Create new migration object and store in cache
     */
    public static function createMigration()
    {
        $database = Database::getInstance();

        self::$migration = $database->createMigration($database->getDBSchemaMode());

        self::saveMigrationToFile(self::$migration);
    }

    /**
     * Create empty migration object and store in cache
     */
    public static function createEmptyMigration()
    {
        self::$migration = new Migration(MigrationType::createEmpty(), []);

        self::saveMigrationToFile(self::$migration);
    }

    /**
     * Remove stored migration object
     */
    public static function removeMigration()
    {
        self::$migration = null;

        FileManager::deleteFile(self::getMigrationFilePath());
    }

    /**
     * Get stored migration object file path
     *
     * @return string
     */
    protected static function getMigrationFilePath()
    {
        return LC_DIR_VAR . '.decorator.migration.php';
    }

    /**
     * Store migration object to file
     *
     * @param Migration $migration
     */
    protected static function saveMigrationToFile(Migration $migration)
    {
        FileManager::write(self::getMigrationFilePath(), serialize($migration));
    }
}
