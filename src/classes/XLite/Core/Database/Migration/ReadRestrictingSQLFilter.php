<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */


namespace XLite\Core\Database\Migration;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Filter\SQLFilter;

/**
 * ReadRestrictingSQLFilter blocks read access to a given set of tables raising exception when such access happens.
 */
class ReadRestrictingSQLFilter extends SQLFilter
{
    private $forbiddenTables;

    /**
     * Gets the SQL query part to add to a query.
     *
     * @param ClassMetaData $targetEntity
     * @param string        $targetTableAlias
     *
     * @return string The constraint SQL if there is available, empty string otherwise.
     */
    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias)
    {
        if (in_array($targetEntity->getTableName(), $this->getForbiddenTables())) {
            throw new UnsupportedDatabaseOperationDuringMaintenanceException();
        }

        return '';
    }

    /**
     * @return mixed
     */
    public function getForbiddenTables()
    {
        return $this->forbiddenTables;
    }

    /**
     * @param mixed $forbiddenTables
     */
    public function setForbiddenTables($forbiddenTables)
    {
        $this->forbiddenTables = $forbiddenTables;
    }
}