<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */


namespace Includes\Database\Migration;


use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Platforms\MySqlPlatform as DefaultMySqlPlatform;

/**
 * Factory class that creates a "migration" platform for a given AbstractPlatform-derived type. Migration platform contains specific SQL generation logic overrides that are required for the migration process to run properly.
 */
class SqlPlatformFactory
{
    public static function getMigrationSqlPlatform(AbstractPlatform $platform)
    {
        if ($platform instanceof DefaultMySqlPlatform) {
            return new MySqlPlatform();
        }

        throw new \RuntimeException('Only MySQL platform is supported');
    }
}