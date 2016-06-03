<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core;

/**
 * Doctrine-based PDO MySQL driver
 */
class PDOMySqlDriver extends \Doctrine\DBAL\Driver\PDOMySql\Driver
{
    /**
     * Get schema manager
     *
     * @param \Doctrine\DBAL\Connection $conn Connection
     *
     * @return \Doctrine\DBAL\Schema\MySqlSchemaManager
     */
    public function getSchemaManager(\Doctrine\DBAL\Connection $conn)
    {
        return new \XLite\Core\MySqlSchemaManager($conn);
    }

    /**
     * Get database platform
     *
     * @return \Doctrine\DBAL\Platforms\AbstractPlatform
     */
    public function getDatabasePlatform()
    {
        return new \XLite\Core\MySqlPlatform();
    }
}
