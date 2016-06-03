<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Repo;

/**
 * The "order_item" model repository
 */
class OrderItem extends \XLite\Model\Repo\ARepo
{
    /**
     * Allowable search params
     */
    const P_ORDER = 'order';
    

    // {{{ Functions to grab top selling products data

    /**
     * Get top sellers depending on certain condition
     *
     * @param \XLite\Core\CommonCell $cnd       Conditions
     * @param boolean                $countOnly Count only flag OPTIONAL
     *
     * @return \Doctrine\ORM\PersistentCollection|integer
     */
    public function getTopSellers(\XLite\Core\CommonCell $cnd, $countOnly = false)
    {
        $result = $this->prepareTopSellersCondition($cnd)->getResult();

        return $countOnly ? count($result) : $result;
    }

    /**
     * Prepare top sellers search condition
     *
     * @param \XLite\Core\CommonCell $cnd Conditions
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function prepareTopSellersCondition(\XLite\Core\CommonCell $cnd)
    {
        list($start, $end) = $cnd->date;

        $qb = $this->createQueryBuilder();

        $qb->addSelect('SUM(o.amount) as cnt')
            ->andWhere($qb->expr()->isNotNull('o.object'))
            ->innerJoin('o.order', 'o1')
            ->innerJoin('o1.paymentStatus', 'ps')
            ->addSelect('o1.date')
            ->andWhere($qb->expr()->in('ps.code', \XLite\Model\Order\Status\Payment::getPaidStatuses()))
            ->setMaxResults($cnd->limit)
            ->addGroupBy('o.sku')
            ->addOrderBy('cnt', 'desc')
            ->addOrderBy('o.name', 'asc');

        if ($cnd->currency) {
            $qb->innerJoin('o1.currency', 'currency', 'WITH', 'currency.currency_id = :currency_id')
                ->setParameter('currency_id', $cnd->currency);
        }

        if (0 < $start) {
            $qb->andWhere('o1.date >= :start')
                ->setParameter('start', $start);
        }

        if (0 < $end) {
            $qb->andWhere('o1.date < :end')
                ->setParameter('end', $end);
        }

        return $qb;
    }

    // }}}

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param integer                    $value        Condition data
     *
     * @return void
     */
    protected function prepareCndOrder(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        if (!empty($value)) {
            $queryBuilder->andWhere('o.order = :order')
                ->setParameter('order', $value);
        }
    }
}
