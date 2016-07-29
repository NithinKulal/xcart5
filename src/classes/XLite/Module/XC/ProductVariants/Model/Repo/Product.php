<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductVariants\Model\Repo;

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

        $productAmountCnd = new \Doctrine\ORM\Query\Expr\Andx();
        $productAmountCnd->add('i.amount > :zero');
        // Product amount counts ONLY IF product have no variants
        // OR have variant with defaultAmount
        $productAmountCnd->add('pv.id IS NULL OR pv.defaultAmount = true');

        $orCnd = new \Doctrine\ORM\Query\Expr\Orx();

        $orCnd->add('p.inventoryEnabled = :disabled');
        $orCnd->add('p.amount > :zero');
        $orCnd->add('pv.amount > :zero');

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
}
