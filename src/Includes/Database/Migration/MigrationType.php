<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */


namespace Includes\Database\Migration;

use Doctrine\DBAL\Schema\ForeignKeyConstraint;
use Doctrine\DBAL\Schema\SchemaDiff;


/**
 * Class MigrationType determines safety of a various DB operations while DB migration is in progress.
 *
 * Safety of the database operation is defined with a table-level granularity.
 */
class MigrationType
{
    private $unsafeReadsTables = [];

    private $unsafeInsertionsTables = [];

    private $unsafeUpdatesTables = [];

    private $unsafeDeletionsTables = [];

    public function __construct(SchemaDiff $schemaDiff)
    {
        foreach ($schemaDiff->removedTables as $removedTable) {
            $this->setUnsafeReads($removedTable->getName());
            $this->setUnsafeWrites($removedTable->getName());
        }

        foreach ($schemaDiff->changedTables as $changedTable) {
            $table = $changedTable->name;

            foreach ($changedTable->addedColumns as $column) {
                // Column definition is a platform-specific DDL, insertions can be unsafe
                if ($column->getColumnDefinition() !== null) {
                    $this->setUnsafeInsertions($table);
                }

                // Insertions will fail if column is NOT NULL and does not specify a DEFAULT value
                if ($column->getNotnull() && $column->getDefault() === null) {
                    $this->setUnsafeInsertions($table);
                }
            }

            /** @var ForeignKeyConstraint[] $addedOrChangedForeignKeys */
            $addedOrChangedForeignKeys = array_merge(
                $changedTable->addedForeignKeys,
                $changedTable->changedForeignKeys
            );

            foreach ($addedOrChangedForeignKeys as $foreignKey) {
                // Only reads and insertions are permitted on the foreign table
                $this->setUnsafeUpdates($foreignKey->getForeignTableName());
                $this->setUnsafeDeletions($foreignKey->getForeignTableName());

                // Only reads and deletions are permitted on the table with changed foreign key
                $this->setUnsafeInsertions($table);
                $this->setUnsafeUpdates($table);
            }

            // New unique indexes make insertions and updates unsafe (potential duplicate key error)
            foreach ($changedTable->addedIndexes as $addedIndex) {
                if ($addedIndex->isUnique()) {
                    $this->setUnsafeInsertions($table);
                    $this->setUnsafeUpdates($table);
                }
            }

            foreach ($changedTable->changedColumns as $column) {
                // Type change changes semantics of reads, writes are also unsafe
                if (
                    $column->hasChanged('type')
                    || $column->hasChanged('unsigned')
                    || $column->hasChanged('length')
                    || $column->hasChanged('fixed')
                    || $column->hasChanged('scale')
                    || $column->hasChanged('precision')
                    || $column->hasChanged('charset')
                    || $column->hasChanged('collation')
                ) {
                    $this->setUnsafeReads($table);
                    $this->setUnsafeWrites($table);
                }

                // If column changed to NOT NULL writes can be unsafe
                if ($column->hasChanged('notnull') && $column->column->getNotnull()) {
                    $this->setUnsafeWrites($table);
                }

                // If column DEFAULT value was removed and column is NOT NULL, insertions are unsafe
                if ($column->hasChanged('default') && $column->column->getDefault() === null && $column->column->getNotnull()) {
                    $this->setUnsafeInsertions($table);
                }

                // 'comment' property changes are safe, skipping them

                $checkedProps = ['type', 'unsigned', 'length', 'fixed', 'scale', 'precision', 'notnull', 'default', 'comment', 'charset', 'collation'];

                // Consider any other column changes unsafe
                if (array_diff($column->changedProperties, $checkedProps)) {
                    $this->setUnsafeReads($table);
                    $this->setUnsafeWrites($table);
                }
            }

            // Renamed/removed columns make reads and writes unsafe
            if (!empty($changedTable->renamedColumns) || !empty($changedTable->removedColumns)) {
                $this->setUnsafeReads($table);
                $this->setUnsafeWrites($table);
            }

            // Indexes that became unique can break insertions and updates
            foreach ($changedTable->changedIndexes as $index) {
                if ($index->isUnique()) {
                    $this->setUnsafeInsertions($table);
                    $this->setUnsafeUpdates($table);

                    break;
                }
            }
        }
    }

    private function setUnsafeReads($table)
    {
        $this->unsafeReadsTables[$table] = $table;
    }

    private function setUnsafeWrites($table)
    {
        $this->setUnsafeInsertions($table);
        $this->setUnsafeUpdates($table);
        $this->setUnsafeDeletions($table);
    }

    private function setUnsafeInsertions($table)
    {
        $this->unsafeInsertionsTables[$table] = $table;
    }

    private function setUnsafeUpdates($table)
    {
        $this->unsafeUpdatesTables[$table] = $table;
    }

    private function setUnsafeDeletions($table)
    {
        $this->unsafeDeletionsTables[$table] = $table;
    }

    public function isReadSafe()
    {
        return empty($this->unsafeReadsTables);
    }

    public function isWriteSafe()
    {
        return empty($this->unsafeInsertionsTables)
               && empty($this->unsafeUpdatesTables)
               && empty($this->unsafeDeletionsTables);
    }

    public function getUnsafeReadsTables()
    {
        return array_values($this->unsafeReadsTables);
    }

    public function areReadsSafe($table)
    {
        return !isset($this->unsafeReadsTables[$table]);
    }

    public function getUnsafeInsertionsTables()
    {
        return array_values($this->unsafeInsertionsTables);
    }

    public function areInsertionsSafe($table)
    {
        return !isset($this->unsafeInsertionsTables[$table]);
    }

    public function getUnsafeUpdatesTables()
    {
        return array_values($this->unsafeUpdatesTables);
    }

    public function areUpdatesSafe($table)
    {
        return !isset($this->unsafeUpdatesTables[$table]);
    }

    public function getUnsafeDeletionsTables()
    {
        return array_values($this->unsafeDeletionsTables);
    }

    public function areDeletionsSafe($table)
    {
        return !isset($this->unsafeDeletionsTables[$table]);
    }

    /**
     * Create empty migration representing no changes to DB
     *
     * @return MigrationType
     */
    public static function createEmpty()
    {
        return new self(new SchemaDiff());
    }
}