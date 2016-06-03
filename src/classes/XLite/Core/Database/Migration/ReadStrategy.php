<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */


namespace XLite\Core\Database\Migration;

use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManagerInterface;
use Includes\Database\Migration\Migration;
use Includes\Database\Migration\MigrationType;

/**
 * Migration-aware ReadStrategy enforces that only safe database read operations are executed during the ongoing database migration. This enforcement is achieved by creating and enabling Doctrine SQL filter that raises an exception when there's a read access to a forbidden table.
 */
class ReadStrategy
{
    const SQL_FILTER_NAME = 'ReadRestrictingSQLFilter';

    /**
     * @var MigrationType
     */
    private $migrationType;

    public function __construct(Migration $migration)
    {
        $this->migrationType = $migration->getMigrationType();
    }

    public function registerSQLFilter(Configuration $configuration)
    {
        $configuration->addFilter(self::SQL_FILTER_NAME, 'XLite\Core\Database\Migration\ReadRestrictingSQLFilter');
    }

    public function enableSQLFilter(EntityManagerInterface $em)
    {
        /** @var ReadRestrictingSQLFilter $filter */
        $filter = $em->getFilters()->enable(self::SQL_FILTER_NAME);

        $filter->setForbiddenTables($this->migrationType->getUnsafeReadsTables());
    }
}