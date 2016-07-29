<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\XPaymentsConnector\Model\Repo\Payment;

/**
 * Payment backend transaction repository
 */
class Transaction extends \XLite\Model\Repo\Payment\BackendTransaction implements \XLite\Base\IDecorator
{
    /**
     * Allowable search params
     */
    const SEARCH_LOGIN            = 'orderId';
    const SEARCH_TRANSACTION_ID = 'transactionId';

    /**
     * Default model alias
     *
     * @var string
     */
    protected $defaultAlias = 'bt';

    /**
     * Create new QueryBuilder instance that is prepopulated for this entity name
     *
     * @param string $alias   Table alias OPTIONAL
     * @param string $indexBy The index for the from. OPTIONAL
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    public function createQueryBuilder($alias = null, $indexBy = null)
    {
        $qb = parent::createQueryBuilder($alias, $indexBy)
            ->innerJoin('bt.payment_transaction', 'pt')
            ->innerJoin('pt.order', 'o');

        return $qb;
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param mixed                      $value        Condition OPTIONAL
     *
     * @return void
     */
    protected function prepareCndOrderId(\Doctrine\ORM\QueryBuilder $queryBuilder, $value = null)
    {
        if ($value) {
            $queryBuilder->andWhere('o.order_id = :order_id')
                ->setParameter('order_id', $value);
        }
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param mixed                      $value        Condition OPTIONAL
     *
     * @return void
     */
    protected function prepareCndTransactionId(\Doctrine\ORM\QueryBuilder $queryBuilder, $value = null)
    {
        if ($value) {
            $queryBuilder->andWhere('pt.transaction_id = :transaction_id')
                ->setParameter('transaction_id', $value);
        }
    }

}

