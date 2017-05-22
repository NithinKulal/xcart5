<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2017-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/module-marketplace-terms-of-use.html for license details.
 */

namespace XLite\Module\QSL\SpecialOffersBase\Model\Repo;

/**
 * Order item surcharges repository
 */
class OrderItem extends \XLite\Model\Repo\OrderItem implements \XLite\Base\IDecorator
{
    /**
     * Returns the sum of order item surcharges
     *
     * @param \XLite\Model\Order $order    Order
     * @param bool               $included Whether to return included surcharges, or excluded OPTIONAL
     *
     * @return float
     */
    public function getSpecialOffersOrderItemSurchargesSum(\XLite\Model\Order $order, $included = false)
    {
        return $this->getSpecialOffersOrderItemSurchargesSumQB($order, $included)
            ->getSingleScalarResult();
    }
    
    /**
     * Returns a query builder to retrieve the sum of order item surcharges
     *
     * @param \XLite\Model\Order $order    Order
     * @param bool               $included Whether to return included surcharges, or excluded OPTIONAL
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function getSpecialOffersOrderItemSurchargesSumQB(\XLite\Model\Order $order, $included = false)
    {
        $qb = $this->createPureQueryBuilder()
            ->linkInner('o.surcharges', 's')
            ->select('sum(s.value) as surcharges_sum')
            ->andWhere('o.order = :order')
            ->setParameter('order', $order)
            ->andWhere('s.available = :available')
            ->setParameter('available', true);
        
        if (!is_null($included)) {
            $qb->andWhere('s.include = :included')
                ->setParameter('included', $included);
        }

        return $qb;
    }

}
