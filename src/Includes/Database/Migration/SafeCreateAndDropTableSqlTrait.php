<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */


namespace Includes\Database\Migration;

use Doctrine\DBAL\Event\SchemaDropTableEventArgs;
use Doctrine\DBAL\Events;
use Doctrine\DBAL\Schema\Table;

/**
 * Trait that overrides \Doctrine\DBAL\Platforms\AbstractPlatform's SQL generation logic so that:
 * - DROP TABLE tbl is replaced with DROP TABLE IF EXISTS tbl
 * - CREATE TABLE tbl is prepended with DROP TABLE IF EXISTS tbl
 */
trait SafeCreateAndDropTableSqlTrait
{
    /**
     * Returns the SQL statement(s) to create a table with the specified name, columns and constraints
     * on this platform.
     *
     * @param \Doctrine\DBAL\Schema\Table   $table
     * @param integer                       $createFlags
     *
     * @return array The sequence of SQL statements.
     *
     * @throws \Doctrine\DBAL\DBALException
     * @throws \InvalidArgumentException
     */
    public function getCreateTableSQL(Table $table, $createFlags = self::CREATE_INDEXES)
    {
        $tableName = $table->getQuotedName($this);

        return array_merge(["DROP TABLE IF EXISTS $tableName"], parent::getCreateTableSQL($table, $createFlags));
    }

    /**
     * Returns the SQL snippet to drop an existing table.
     *
     * @param \Doctrine\DBAL\Schema\Table|string $table
     *
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    public function getDropTableSQL($table)
    {
        $tableArg = $table;

        if ($table instanceof Table) {
            $table = $table->getQuotedName($this);
        } elseif (!is_string($table)) {
            throw new \InvalidArgumentException('getDropTableSQL() expects $table parameter to be string or \Doctrine\DBAL\Schema\Table.');
        }

        if (null !== $this->_eventManager && $this->_eventManager->hasListeners(Events::onSchemaDropTable)) {
            $eventArgs = new SchemaDropTableEventArgs($tableArg, $this);
            $this->_eventManager->dispatchEvent(Events::onSchemaDropTable, $eventArgs);

            if ($eventArgs->isDefaultPrevented()) {
                return $eventArgs->getSql();
            }
        }

        return 'DROP TABLE IF EXISTS ' . $table;
    }
}