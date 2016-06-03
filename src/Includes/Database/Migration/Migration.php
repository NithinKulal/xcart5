<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */


namespace Includes\Database\Migration;


/**
 * Class MigrationType determines a safeness of various DB operations while DB migration is in progress.
 *
 * Operation safeness is defined on a table level granularity.
 */
class Migration
{
    /** @var MigrationType */
    private $migrationType;

    /** @var string[] */
    private $queries;

    public function __construct($migrationType, $queries)
    {
        $this->migrationType = $migrationType;
        $this->queries       = $queries;
    }

    /**
     * @return MigrationType
     */
    public function getMigrationType()
    {
        return $this->migrationType;
    }

    /**
     * @return string[]
     */
    public function getQueries()
    {
        return $this->queries;
    }
}