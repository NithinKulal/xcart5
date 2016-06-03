<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */


namespace Includes\Database\Migration;


use Doctrine\DBAL\Schema\ColumnDiff;
use Doctrine\DBAL\Schema\Comparator;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\SchemaDiff;
use Doctrine\DBAL\Schema\Table;

/**
 * Schema comparator that decorates standard Doctrine\DBAL\Schema\Comparator to bring the ability to protect specific tables, columns, foreign keys and indexes from removal.
 */
class SchemaComparator
{
    /**
     * @var Comparator
     */
    private $comparator;

    /**
     * @var string[]
     */
    private $disabledTables;

    /**
     * @var string[][]
     */
    private $disabledColumns;

    /**
     * @var string[]
     */
    private $enabledTables;

    /**
     * @var string
     */
    private $tablePrefix;

    /**
     * DisabledStructuresPreservingComparator constructor.
     * @param Comparator $comparator
     * @param string[]   $disabledTables
     * @param string[]   $disabledColumns
     * @param string[]   $enabledTables
     * @param string     $tablePrefix
     */
    public function __construct(Comparator $comparator, $disabledTables, $disabledColumns, $enabledTables, $tablePrefix)
    {
        $this->tablePrefix     = $tablePrefix;
        $this->comparator      = $comparator;
        $this->disabledTables  = array_map(function ($t) { return $this->tablePrefix . $t; }, $disabledTables);
        $this->disabledColumns = $disabledColumns;
        $this->enabledTables   = array_map(function ($t) { return $this->tablePrefix . $t; }, $enabledTables);
    }

    /**
     * Returns a SchemaDiff object containing the differences between the schemas $fromSchema and $toSchema.
     *
     * The returned differences are returned in such a way that they contain the
     * operations to change the schema stored in $fromSchema to the schema that is
     * stored in $toSchema.
     *
     * @param Schema $fromSchema
     * @param Schema $toSchema
     *
     * @return SchemaDiff
     */
    public function compare(Schema $fromSchema, Schema $toSchema)
    {
        return $this->preserveTablesAndColumns(
            $this->comparator->compare($fromSchema, $toSchema)
        );
    }

    /**
     * Preserve ("un-remove") specific tables, columns, foreign keys and indexes that otherwise would be deleted by the default Doctrine's schema diffing procedure
     *
     * @param SchemaDiff $diff Schema diff object
     *
     * @return array
     */
    protected function preserveTablesAndColumns(SchemaDiff $diff)
    {
        if ($this->disabledTables) {

            // Do not drop disabled tables and their foreign keys:

            foreach ($diff->changedTables as $changedTable) {
                if (in_array($changedTable->name, $this->disabledTables)) {
                    $changedTable->addedForeignKeys   = [];
                    $changedTable->removedForeignKeys = [];
                }
            }

            foreach ($diff->newTables as $newTable) {
                if (in_array($newTable->getName(), $this->disabledTables)) {
                    foreach ($newTable->getForeignKeys() as $key) {
                        $newTable->removeForeignKey($key->getName());
                    }
                }
            }

            foreach ($diff->orphanedForeignKeys as $k => $key) {
                if (in_array($key->getLocalTableName(), $this->disabledTables)) {
                    unset ($diff->orphanedForeignKeys[$k]);
                }
            }

            if (!empty($diff->removedTables)) {
                $diff->removedTables = array_filter($diff->removedTables, function (Table $table) {
                    return !in_array($table->getName(), $this->disabledTables)
                           && !in_array($table->getName(), $this->enabledTables);
                });
            }

            // Do not drop foreign keys referencing disabled tables
            // (dropped foreign keys may result from records in orphanedForeignKeys or changedTables)

            $preservedForeignKeys = [];

            foreach ($diff->orphanedForeignKeys as $k => $key) {
                if (in_array($key->getForeignTableName(), $this->disabledTables)) {
                    unset($diff->orphanedForeignKeys[$k]);

                    $preservedForeignKeys[] = substr($key->getName(), 3); // Cut off 'FK_' from key name
                }
            }

            foreach ($diff->changedTables as $changedTable) {
                foreach ($changedTable->removedForeignKeys as $k => $key) {
                    if (in_array($key->getForeignTableName(), $this->disabledTables)) {
                        unset($changedTable->removedForeignKeys[$k]);

                        $preservedForeignKeys[] = substr($key->getName(), 3); // Cut off 'FK_' from key name
                    }
                }
            }

            if ($preservedForeignKeys) {
                foreach ($diff->changedTables as $changedTable) {
                    foreach ($changedTable->removedIndexes as $k => $index) {
                        // Hack for different names of indexes (IDX_*) and foreign keys (FK_*) in Windows
                        // See BUG-1963
                        $indexForAForeignKey = (bool)array_filter($preservedForeignKeys, function ($fkName) use ($index) {
                            return strpos($index->getName(), $fkName) !== false;
                        });

                        if ($indexForAForeignKey) {
                            unset($changedTable->removedIndexes[$k]);
                        }
                    }
                }
            }
        }

        // Do not drop disabled columns, change them to nullable:

        foreach ($this->disabledColumns as $table => $fields) {
            foreach ($fields as $columnName => $change) {
                $tableName = $this->tablePrefix . $table;

                if (
                    isset($diff->changedTables[strtolower($tableName)])
                    && isset($diff->changedTables[$tableName]->removedColumns[strtolower($columnName)])
                ) {
                    $changedTable  = $diff->changedTables[strtolower($tableName)];
                    $removedColumn = $changedTable->removedColumns[strtolower($columnName)];

                    unset($changedTable->removedColumns[strtolower($columnName)]);

                    if ($removedColumn->getNotnull() && null === $removedColumn->getDefault()) {
                        $default = $this->getColumnDefaultValue($removedColumn->getType());
                        if (null !== $default) {
                            $changedTable->changedColumns[strtolower($columnName)] =
                                new ColumnDiff($columnName, $removedColumn->setDefault($default), ['default']);
                        }
                    }
                }
            }
        }

        return $diff;
    }

    /**
     * Get default value for column depending on its type
     *
     * @param \Doctrine\DBAL\Types\Type $columnType
     *
     * @return null|string
     */
    protected function getColumnDefaultValue($columnType)
    {
        switch($columnType->getBindingType()) {

            case \PDO::PARAM_INT:
            case \PDO::PARAM_BOOL: {
                $result = 0;
                break;
            }

            case \PDO::PARAM_STR: {
                $result = '';
                break;
            }

            case \PDO::PARAM_LOB:
            default: {
                $result = null;
            }
        }

        return $result;
    }
}
