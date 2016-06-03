<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Repo;

/**
 * Cart repository
 */
class Cart extends \XLite\Model\Repo\ARepo
{
    /**
     * Mark cart model as order
     *
     * @param integer $orderId Order id
     *
     * @return boolean
     */
    public function markAsOrder($orderId)
    {
        $stmt = $this->defineMarkAsOrderQuery($orderId);

        return $stmt && $stmt->execute() && 0 < $stmt->rowCount();
    }

    /**
     * Get one cart for customer interface
     *
     * @param integer $id Cart id
     *
     * @return \XLite\Model\Cart
     */
    public function findOneForCustomer($id)
    {
        return $this->defineFindOneForCustomerQuery($id)->getSingleResult();
    }

    /**
     * Define query for markAsOrder() method
     *
     * @param integer $orderId Order id
     *
     * @return \Doctrine\DBAL\Statement|void
     */
    protected function defineMarkAsOrderQuery($orderId)
    {
        $stmt = $this->_em->getConnection()->prepare(
            'UPDATE ' . $this->_class->getTableName() . ' '
            . 'SET is_order = :flag '
            . 'WHERE order_id = :id'
        );

        if ($stmt) {
            $stmt->bindValue(':flag', 1);
            $stmt->bindValue(':id', $orderId);

        } else {
            $stmt = null;
        }

        return $stmt;
    }

    /**
     * Define query for findOneForCustomer() method
     *
     * @param integer $id Cart id
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineFindOneForCustomerQuery($id)
    {
        return $this->createQueryBuilder()
            ->addSelect('profile')
            ->addSelect('currency')
            ->linkLeft('c.profile')
            ->linkLeft('c.currency')
            ->andWhere('c.order_id = :id')
            ->setParameter('id', $id)
            ->setMaxResults(1);
    }

}
