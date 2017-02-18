<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductVariants\Model\Repo;
use Doctrine\ORM\Query\Expr\Andx;
use Doctrine\ORM\Query\Expr\Orx;

/**
 * Product model repository
 */
abstract class Product extends \XLite\Model\Repo\Product implements \XLite\Base\IDecorator
{
    const VARIANT_SKU_FIELD = 'pv.sku';

    /**
     * Add inventory condition to search in-stock products
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     *
     * @return void
     */
    protected function prepareCndInventoryIn(\Doctrine\ORM\QueryBuilder $queryBuilder)
    {
        $queryBuilder->linkLeft('p.variants', 'pv');

        $orCnd = new Orx([
            'p.inventoryEnabled = :disabled',
            'pv.amount > :zero',
            new Andx([
                'p.amount > :zero',
                new Orx([
                    'pv.id IS NULL',
                    'pv.defaultAmount = true'
                ])
            ]),
        ]);

        $queryBuilder->andWhere($orCnd)
            ->setParameter('disabled', false)
            ->setParameter('zero', 0);
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param string                     $value        Condition data
     *
     * @return void
     */
    protected function prepareCndSubstring(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        parent::prepareCndSubstring($queryBuilder, $value);

        $queryBuilder->linkLeft('p.variants', 'pv');
    }

    /**
     * Return fields set for SKU search
     *
     * @return array
     */
    protected function getSubstringSearchFieldsBySKU()
    {
        return array_merge(
            parent::getSubstringSearchFieldsBySKU(),
            array(
                static::VARIANT_SKU_FIELD,
            )
        );
    }

    /**
     * Assign prica range-based search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder
     * @param float                      $min          Minimum
     * @param float                      $max          Maximum
     *
     * @return void
     */
    protected function assignPriceRangeCondition(\Doctrine\ORM\QueryBuilder $queryBuilder, $min, $max)
    {
        if (!\XLite::isAdminZone() && \XLite\Module\XC\ProductVariants\Main::isDisplayPriceAsRange()) {
            if (null !== $min) {
                $queryBuilder->andWhere($this->getCalculatedField($queryBuilder, 'maxPrice') . ' >= :minPrice')
                    ->setParameter('minPrice', (float) $min);
            }

            if (null !== $max) {
                $queryBuilder->andWhere($this->getCalculatedField($queryBuilder, 'minPrice') . ' <= :maxPrice')
                    ->setParameter('maxPrice', (float) $max);
            }
        } elseif (null !== $min || null !== $max) {
            parent::assignPriceRangeCondition($queryBuilder, $min, $max);
        }
    }

    /**
     * Define calculated minimal price definition DQL
     *
     * @param \XLite\Model\QueryBuilder\AQueryBuilder $queryBuilder Query builder
     * @param string                                  $alias        Main alias
     *
     * @return string
     */
    protected function defineCalculatedMinPriceDQL(\XLite\Model\QueryBuilder\AQueryBuilder $queryBuilder, $alias)
    {
        $profile = \XLite\Core\Auth::getInstance()->getProfile();
        if ($profile
            && $profile->getMembership()
        ) {
            $queryBuilder->getAllAliases()->innerJoin(
                $alias . '.quickData',
                'qdMinPrice',
                'WITH',
                'qdMinPrice.membership = :qdMembership'
            )->setParameter('qdMembership', $profile->getMembership());

        } else {
            $queryBuilder->innerJoin(
                $alias . '.quickData',
                'qdMinPrice',
                'WITH',
                'qdMinPrice.membership is null'
            );
        }

        return 'qdMinPrice.minPrice';
    }

    /**
     * Define calculated maximal price definition DQL
     *
     * @param \XLite\Model\QueryBuilder\AQueryBuilder $queryBuilder Query builder
     * @param string                                  $alias        Main alias
     *
     * @return string
     */
    protected function defineCalculatedMaxPriceDQL(\XLite\Model\QueryBuilder\AQueryBuilder $queryBuilder, $alias)
    {
        $profile = \XLite\Core\Auth::getInstance()->getProfile();
        if ($profile
            && $profile->getMembership()
        ) {
            $queryBuilder->innerJoin(
                $alias . '.quickData',
                'qdMaxPrice',
                'WITH',
                'qdMaxPrice.membership = :qdMembership'
            )->setParameter('qdMembership', $profile->getMembership());

        } else {
            $queryBuilder->innerJoin(
                $alias . '.quickData',
                'qdMaxPrice',
                'WITH',
                'qdMaxPrice.membership is null'
            );
        }

        return 'qdMaxPrice.maxPrice';
    }

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

            if ('p.price' === $sort && !\XLite::isAdminZone() && \XLite\Module\XC\ProductVariants\Main::isDisplayPriceAsRange()) {
                $sort = $this->getCalculatedFieldAlias($queryBuilder, 'minPrice');

                $queryBuilder->addOrderBy($sort, $order);
            } else {
                parent::prepareCndOrderBy($queryBuilder, $value);
            }

        }
    }
}
