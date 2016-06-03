<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Bestsellers\Model\Repo;

/**
 * The "OrderItem" model repository
 */
class Product extends \XLite\Model\Repo\Product implements \XLite\Base\IDecorator
{
    /**
     * Defines bestsellers products collection
     *
     * @param \XLite\Core\CommonCell $cnd   Search condition
     * @param integer                $count Number of products to get OPTIONAL
     * @param integer                $cat   Category identificator OPTIONAL
     *
     * @return array
     */
    public function findBestsellers(\XLite\Core\CommonCell $cnd, $count = 0, $cat = 0)
    {
        return $this->defineBestsellersQuery($cnd, $count, $cat)->getOnlyEntities();
    }

    /**
     * Prepares query builder object to get bestsell products
     *
     * @param \XLite\Core\CommonCell $cnd   Search condition
     * @param integer                $count Number of products to get
     * @param integer                $cat   Category identificator
     *
     * @return \Doctrine\ORM\QueryBuilder Query builder object
     */
    protected function defineBestsellersQuery(\XLite\Core\CommonCell $cnd, $count, $cat)
    {
        list($sort, $order) = $cnd->{self::P_ORDER_BY};

        $qb = $this->createQueryBuilder()
            ->andWhere('p.sales > 0')
            ->addGroupBy('p.product_id')
            ->addOrderBy('p.sales', 'desc')
            ->addOrderBy($sort, $order);

        if (0 < $count) {
            $qb->setMaxResults($count);
        } elseif (isset($cnd->{self::P_LIMIT})) {
            $qb->setFrameResults($cnd->{self::P_LIMIT});
        }

        if (0 < $cat) {
            $qb->linkLeft('p.categoryProducts', 'cp')->linkLeft('cp.category', 'c');
            \XLite\Core\Database::getRepo('XLite\Model\Category')->addSubTreeCondition($qb, $cat);
        }

        return \XLite\Core\Database::getRepo('XLite\Model\Product')->assignExternalEnabledCondition($qb, 'p');
    }
}
