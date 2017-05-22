<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Reviews\Model\Repo;

/**
 * The Product model repository
 */
class Product extends \XLite\Model\Repo\Product implements \XLite\Base\IDecorator
{
    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param array                      $value        Condition data
     *
     * @return void
     */
    protected function prepareCndOrderBy(\Doctrine\ORM\QueryBuilder $queryBuilder, array $value)
    {
        if (!$this->isCountSearchMode()) {
            list($sort, $order) = $this->getSortOrderValue($value);
            if ('r.rating' == $sort) {
                $queryBuilder->linkLeft('p.reviews', 'r', \Doctrine\ORM\Query\Expr\Join::WITH, 'r.status = 1');
                $queryBuilder->addSelect('(SUM(r.rating) / COUNT(r.rating)) as rsm, COUNT(DISTINCT r.id) as rates_count');
                $sort = 'rsm';
                $queryBuilder->addOrderBy($sort, $order);
                $sort = 'rates_count';
                $queryBuilder->addOrderBy($sort, 'DESC');
            } else {
                parent::prepareCndOrderBy($queryBuilder, $value);
            }
        }
    }
}
