<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Repo;

/**
 * Event tasks repository
 */
class EventTask extends \XLite\Model\Repo\ARepo
{
    /**
     * Query limit
     */
    const QUERY_LIMIT = 10;

    /**
     * Find query 
     *
     * @param integer $limit Tasks limit OPTIONAL
     * 
     * @return array
     */
    public function findQuery($limit = self::QUERY_LIMIT)
    {
        return $this->defineFindQuery($limit)->getResult();
    }

    /**
     * Clean event tasks
     *
     * @param string    $eventName  Event name
     * @param int       $exceptId   Task id
     *
     * @return void
     */
    public function cleanTasks($eventName, $exceptId)
    {
        $this->getQueryBuilder()
            ->delete($this->_entityName, 'e')
            ->andWhere('e.name = :eventName')
            ->andWhere('e.id != :exceptId')
            ->setParameter('eventName', $eventName)
            ->setParameter('exceptId', $exceptId)
            ->execute();
    }

    /**
     * Define query for findQuery() method
     *
     * @param integer $limit Tasks limit
     * 
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineFindQuery($limit)
    {
        return $this->createQueryBuilder('e')
            ->setMaxResults($limit)
            ->orderBy('e.id', 'asc');
    }
}
