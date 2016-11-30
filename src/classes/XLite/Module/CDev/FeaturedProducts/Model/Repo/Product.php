<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\FeaturedProducts\Model\Repo;

/**
 * Featured Product repository
 */
class Product extends \XLite\Model\Repo\Product implements \XLite\Base\IDecorator
{
    // {{{ Search

    const SEARCH_FEATURED_CATEGORY_ID = 'featuredCategoryId';

    /**
     * Prepare certain search condition
     *
     * @param \XLite\Model\QueryBuilder\AQueryBuilder $qb    Query builder to prepare
     * @param integer                                 $value Condition data
     *
     * @return void
     */
    protected function prepareCndFeaturedCategoryId(\XLite\Model\QueryBuilder\AQueryBuilder $qb, $value)
    {
        $qb->linkInner('p.featuredProducts')
            ->andWhere('featuredProducts.category = :featuredCategoryId')
            ->setParameter('featuredCategoryId', $value);
    }

    // }}}
}
