<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CanadaPost\Model\Repo;

/**
 * Class represents a Canada Post return repository
 */
class ProductsReturn extends \XLite\Model\Repo\ARepo
{
    /**
     * Allowable search params
     */
    const P_STATUS     = 'status';
    const P_DATE_RANGE = 'dateRange';
    const P_SUBSTRING  = 'substring';
    
    // {{{ Prepare search conditions

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $qb        Query builder to prepare
     * @param string                     $value     Condition data
     * @param boolean                    $countOnly "Count only" flag
     *
     * @return void
     */
    protected function prepareCndStatus(\Doctrine\ORM\QueryBuilder $qb, $value, $countOnly)
    {
        if (!empty($value)) {

            if (is_array($value)) {

                $qb->andWhere($qb->expr()->in('r.status', $value));

            } else {

                $qb->andWhere('r.status = :status')
                    ->setParameter('status', $value);
            }
        }
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param integer                    $value        Condition data
     *
     * @return void
     */
    protected function prepareCndDateRange(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        if ($value && is_array($value)) {
            list($start, $end) = $value;

            if ($start) {
                $queryBuilder->andWhere('r.date >= :start')
                    ->setParameter('start', $start);
            }

            if ($end) {
                $queryBuilder->andWhere('r.date <= :end')
                    ->setParameter('end', $end);
            }
        }
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $qb        Query builder to prepare
     * @param integer                    $value     Condition data
     * @param boolean                    $countOnly "Count only" flag
     *
     * @return void
     */
    protected function prepareCndSubstring(\Doctrine\ORM\QueryBuilder $qb, $value, $countOnly)
    {
        if (!empty($value)) {
            $this->linkInner('r.order', 'o');
            $number = $value;

            if (preg_match('/^\d+$/Ss', $number)) {
                $number = intval($number);
            }

            $qb->andWhere($qb->expr()->orX('r.id = :substring', 'o.orderNumber = :substring'))
                ->setParameter('substring', $number);
        }
    }

    // }}}
}