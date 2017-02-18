<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\ProductAdvisor\Model\Repo;

/**
 * The Product model repository extension
 */
class Product extends \XLite\Model\Repo\Product implements \XLite\Base\IDecorator
{
    /**
     * Allowable search params
     */
    const P_ARRIVAL_DATE      = 'arrivalDate';
    const P_PROFILE_ID        = 'profileId';
    const P_VIEWED_PRODUCT_ID = 'viewedProductId';
    const P_PA_GROUP_BY       = 'paGroupBy';
    const P_PRODUCT_IDS       = 'productIds';

    const SEARCH_MODE_INDEXED = 'searchModeIndexed';

    // {{{ Search functionallity extension

    /**
     * Disable checking if product is up-to-date
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder object
     * @param string                     $alias        Entity alias OPTIONAL
     */
    protected function addDateCondition(\Doctrine\ORM\QueryBuilder $queryBuilder, $alias = null)
    {
        if (!\XLite\Core\Config::getInstance()->CDev->ProductAdvisor->cs_enabled) {
            parent::addDateCondition($queryBuilder, $alias);
        }
    }

    /**
     * Get search modes handlers
     *
     * @return array
     */
    protected function getSearchModes()
    {
        return array_merge(
            parent::getSearchModes(),
            array(
                static::SEARCH_MODE_INDEXED     => 'searchIndexed',
            )
        );
    }

    /**
     * Search result routine.
     *
     * @return \Doctrine\ORM\PersistentCollection
     */
    protected function searchIndexed()
    {
        $queryBuilder = $this->postprocessSearchIndexedQueryBuilder($this->searchState['queryBuilder']);

        return $queryBuilder->getObjectResult();
    }

    /**
     * Prepare queryBuilder for searchResult() method
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function postprocessSearchIndexedQueryBuilder($queryBuilder)
    {
        $key = $this->getSearchPrimaryFields($queryBuilder);

        $queryBuilder->indexBy($this->getMainAlias($queryBuilder), $this->getMainAlias($queryBuilder) . '.' . $this->getPrimaryKeyField());
        $queryBuilder->groupBy($key);

        return $queryBuilder;
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param array                      $value        Condition data
     */
    protected function prepareCndArrivalDate(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        if (is_array($value) && 2 === count($value)) {

            $min = (int) trim(array_shift($value));
            $max = (int) trim(array_shift($value));

            $min = (0 === $min ? null : $min);
            $max = (0 === $max ? null : $max);

            $this->assignArrivalDateRangeCondition($queryBuilder, $min, $max);
        }
    }

    /**
     * Assign arrivalDate range-based search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder
     * @param float                      $min          Minimum OPTIONAL
     * @param float                      $max          Maximum OPTIONAL
     */
    protected function assignArrivalDateRangeCondition(\Doctrine\ORM\QueryBuilder $queryBuilder, $min = null, $max = null)
    {
        if (null !== $min) {
            $queryBuilder->andWhere('p.arrivalDate > :minDate')
                ->setParameter('minDate', (float) $min);
        }

        if (null !== $max) {
            $queryBuilder->andWhere('p.arrivalDate < :maxDate')
                ->setParameter('maxDate', (float) $max);
        }
    }

    // }}}

    // {{{ findProductsOrderedByUsers

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param array                      $value        Condition data
     */
    protected function prepareCndProfileId(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        $queryBuilder->addSelect('COUNT(p.product_id) as cnt')
            ->innerJoin('p.order_items', 'oi')
            ->innerJoin('oi.order', 'o');

        if (is_array($value) && 1 < count($value)) {
            $queryBuilder->innerJoin(
                'o.orig_profile',
                'profile',
                'WITH',
                'profile.profile_id IN (' . implode(',', $value) . ')'
            );

        } else {
            $queryBuilder->innerJoin('o.orig_profile', 'profile', 'WITH', 'profile.profile_id = :profileId')
                ->setParameter('profileId', is_array($value) ? array_pop($value) : $value);
        }
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param string                     $value        Parameter for GROUP BY
     */
    protected function prepareCndPaGroupBy(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        $queryBuilder->groupBy($value);
    }

    // }}}

    // {{{ findBoughtProducts

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param array                      $value        Condition data
     */
    protected function prepareCndViewedProductId(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        $queryBuilder->innerJoin('p.purchase_stats', 'bp');

        if (is_array($value) && 1 < count($value)) {
            $queryBuilder->innerJoin(
                'bp.viewed_product',
                'vp',
                'WITH',
                'vp.product_id IN (' . implode(',', $value) . ')'
            );

        } else {
            $queryBuilder->innerJoin('bp.viewed_product', 'vp', 'WITH', 'vp.product_id = :productId')
                ->setParameter('productId', is_array($value) ? array_pop($value) : $value);
        }
    }

    // }}}

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param array                      $value        Condition data
     */
    protected function prepareCndProductIds(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        $queryBuilder->andWhere($queryBuilder->expr()->in('p.product_id', $value));
    }
}
