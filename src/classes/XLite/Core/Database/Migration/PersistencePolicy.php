<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */


namespace XLite\Core\Database\Migration;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\UnitOfWork;
use Includes\Database\Migration\MigrationType;

/**
 * Migration-aware PersistencePolicy determines if the changes in the given UnitOfWork can be persisted to the database given that the migration is being performed in parallel.
 */
class PersistencePolicy
{
    /**
     * @var MigrationType
     */
    private $migrationType;

    public function __construct(MigrationType $migrationType)
    {
        $this->migrationType = $migrationType;
    }

    public function isPersistencePermitted(EntityManagerInterface $em, UnitOfWork $unitOfWork)
    {
        foreach ($unitOfWork->getScheduledEntityInsertions() as $entity) {
            $tableName = $em->getClassMetadata(get_class($entity))->getTableName();

            if (!$this->migrationType->areInsertionsSafe($tableName)) {
                return false;
            }
        }

        foreach ($unitOfWork->getScheduledEntityUpdates() as $entity) {
            $tableName = $em->getClassMetadata(get_class($entity))->getTableName();

            if (!$this->migrationType->areUpdatesSafe($tableName)) {
                return false;
            }
        }

        foreach ($unitOfWork->getScheduledEntityDeletions() as $entity) {
            $tableName = $em->getClassMetadata(get_class($entity))->getTableName();

            if (!$this->migrationType->areDeletionsSafe($tableName)) {
                return false;
            }
        }

        return true;
    }
}