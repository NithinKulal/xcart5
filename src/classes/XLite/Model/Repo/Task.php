<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Repo;

/**
 * Tasks repository
 */
class Task extends \XLite\Model\Repo\ARepo
{
    /**
     * Get current query
     *
     * @return \Iterator
     */
    public function getCurrentQuery()
    {
        return $this->defineGetCurrentQueryQuery()->iterate();
    }

    /**
     * Define query for getCurrentQuery() method
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineGetCurrentQueryQuery()
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.triggerTime < :time OR t.triggerTime = 0')
            ->setParameter('time', \XLite\Core\Converter::time());
    }
}
