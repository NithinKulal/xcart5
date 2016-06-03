<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\BulkEditing\Model\Repo;

/**
 * The Product model repository
 */
class Product extends \XLite\Model\Repo\Product implements \XLite\Base\IDecorator
{
    const P_IDS = 'ids';

    /**
     * Checks if condition key is restricted to use
     *
     * @return array
     */
    public function getBulkEditRestrictedCondition()
    {
        return [
            'limit',
            'sortBy',
            'orderBy',
        ];
    }
    
    /**
     * Set selection of item ids for export
     *
     * @param \XLite\Core\CommonCell $filter
     */
    public function setBulkEditFilter($filter)
    {
        if ($filter) {
            $this->clearBulkEditFilter();

            $cnd = \XLite\Core\Session::getInstance()->{$filter['conditionCell']};
            if ($cnd) {
                foreach ($this->getBulkEditRestrictedCondition() as $cndName) {
                    if (isset($cnd->{$cndName})) {
                        unset($cnd->{$cndName});
                    }
                }
            }

            /** @var array $ids */
            $ids = $filter['selected']
                ?: $this->search($cnd, static::SEARCH_MODE_IDS);
            \Includes\Utils\ArrayManager::eachCons($ids, 1000, [$this, 'writeBulkEditIds']);
        }
    }

    /**
     * Write ids to xcPendingBulkEdit field
     * Must be public cause used by reference
     *
     * @param array $ids Array of exported ids
     */
    public function writeBulkEditIds(array $ids)
    {
        $expr = new \Doctrine\ORM\Query\Expr();
        $alias = $this->getDefaultAlias();

        $updateQb = $this->getQueryBuilder()
            ->update($this->_entityName, $alias)
            ->set($alias . '.xcPendingBulkEdit', 1)
            ->where(
                $expr->in(
                    $alias . '.' . $this->getPrimaryKeyField(),
                    ':ids'
                )
            )
            ->setParameter('ids', $ids);

        $updateQb->execute();
    }

    /**
     * Count items for export routine
     *
     * @return integer
     */
    public function countForBulkEdit()
    {
        return (int) $this->defineCountForBulkEditQuery()
            ->getSingleScalarResult();
    }

    /**
     * Set selection of item ids for export
     */
    public function clearBulkEditFilter()
    {
        $alias = $this->getDefaultAlias();
        $this->getQueryBuilder()
            ->update($this->_entityName, $alias)
            ->set($alias . '.xcPendingBulkEdit', 0)
            ->execute();
    }

    /**
     * Define query builder for COUNT query
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineCountForBulkEditQuery()
    {
        $qb = $this->createPureQueryBuilder()
            ->select(
                'COUNT(DISTINCT ' . $this->getDefaultAlias() . '.' . $this->getPrimaryKeyField() . ')'
            );

        $qb->andWhere($qb->getMainAlias() . '.xcPendingBulkEdit = 1');

        return $qb;
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param mixed                      $value        Condition data
     *
     * @return void
     */
    protected function prepareCndIds(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        $queryBuilder->where(
            $queryBuilder->expr()->in(
                'p.' . $this->getPrimaryKeyField(),
                ':ids'
            )
        )
            ->setParameter('ids', $value);

    }

    // {{{ Iterator

    /**
     * Define items iterator
     *
     * @param integer $position Position OPTIONAL
     *
     * @return \Doctrine\ORM\Internal\Hydration\IterableResult
     */
    public function getBulkEditIterator($position = 0)
    {
        return $this->defineBulkEditIteratorQueryBuilder($position)
            ->iterate();
    }

    /**
     * Define export iterator query builder
     *
     * @param integer $position Position
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineBulkEditIteratorQueryBuilder($position)
    {
        $qb = $this->createPureQueryBuilder()
            ->setFirstResult($position)
            ->setMaxResults(1000000000);

        $qb->andWhere($qb->getMainAlias() . '.xcPendingBulkEdit = 1');

        return $qb;
    }

    // }}}
}
