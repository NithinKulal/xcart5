<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */


namespace Includes\Database\Migration;

use Doctrine\DBAL\Schema\Comparator;
use Doctrine\DBAL\Schema\SchemaDiff;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use XLite\Core\Database;

class MigrationFactory
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var Database
     */
    private $database;

    public function __construct(EntityManager $em, Database $database)
    {
        $this->em       = $em;
        $this->database = $database;
    }

    /**
     * Create Migration object containing SQL queries that need to be performed and MigrationType object that describes migration safety (in some cases concurrent reads and even writes are permitted while the migration is in progress, MigrationType describes precisely which operations are permitted).
     *
     * @param $mode
     *
     * @return Migration
     */
    public function createMigration($mode)
    {
        $metadata = $this->em->getMetadataFactory()->getAllMetadata();

        $platform = SqlPlatformFactory::getMigrationSqlPlatform($this->em->getConnection()->getDatabasePlatform());

        if ($mode == Database::SCHEMA_CREATE) {
            $schemaTool = new SchemaTool($this->em);

            $diff = $this->getCreateSchemaDiff($metadata);

            $schema  = $schemaTool->getSchemaFromMetadata($metadata);
            $queries = $schema->toSql($platform);

        } else if ($mode == Database::SCHEMA_UPDATE) {
            $diff = $this->getUpdateSchemaDiff($metadata);

            $queries = $diff->toSql($platform);

        } else if ($mode == Database::SCHEMA_DELETE) {
            $schemaTool = new SchemaTool($this->em);

            $queries = $schemaTool->getDropSchemaSQL($metadata);

            $diff = $this->getDropSchemaDiff($metadata);

        } else {
            throw new \RuntimeException('Unsupported migration mode');
        }

        if (!empty($queries)) {
            array_unshift($queries, 'SET UNIQUE_CHECKS=0, FOREIGN_KEY_CHECKS=0');
            array_push($queries, 'SET UNIQUE_CHECKS=1, FOREIGN_KEY_CHECKS=1');
        }

        return new Migration(new MigrationType($diff), $queries);
    }

    /**
     * Calculate DB schema difference between the current state of DB and what is described by mapping metadata
     *
     * @param array $classes The classes to consider.
     *
     * @return SchemaDiff
     */
    protected function getUpdateSchemaDiff(array $classes)
    {
        $schemaTool = new SchemaTool($this->em);

        $sm = $this->em->getConnection()->getSchemaManager();

        $fromSchema = $sm->createSchema();
        $toSchema   = $schemaTool->getSchemaFromMetadata($classes);

        list($disabledTables, $disabledColumns) = $this->database->getDisabledStructuresToStore();
        $enabledTables = !empty($disabledTables) ? $this->database->getEnabledStructuresToStore()[0] : [];

        $comparator = new SchemaComparator(
            new Comparator(),
            $disabledTables,
            $disabledColumns,
            $enabledTables,
            $this->database->getTablePrefix()
        );

        $schemaDiff = $comparator->compare($fromSchema, $toSchema);

        return $schemaDiff;
    }

    /**
     * Get DB schema difference between an empty DB and what is described by mapping metadata
     *
     * @param array $classes The classes to consider.
     *
     * @return SchemaDiff
     */
    protected function getCreateSchemaDiff(array $classes)
    {
        $schemaTool = new SchemaTool($this->em);

        $schema = $schemaTool->getSchemaFromMetadata($classes);

        return new SchemaDiff($schema->getTables());
    }

    /**
     * Calculate DB schema difference between the current state of DB and what is described by mapping metadata
     *
     * @param array $classes The classes to consider.
     *
     * @return SchemaDiff
     */
    protected function getDropSchemaDiff(array $classes)
    {
        $schemaTool = new SchemaTool($this->em);

        $schema = $schemaTool->getSchemaFromMetadata($classes);

        return new SchemaDiff([], [], $schema->getTables());
    }
}