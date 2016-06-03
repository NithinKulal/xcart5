<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductFilter\Model\Repo;

/**
 * The "Attribute" model repository
 *
 */
abstract class Attribute extends \XLite\Model\Repo\Attribute implements \XLite\Base\IDecorator
{
    const SEARCH_VISIBLE = 'visible';

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param mixed                      $value        Condition OPTIONAL
     *
     * @return void
     */
    protected function prepareCndVisible(\Doctrine\ORM\QueryBuilder $queryBuilder, $value = null)
    {
        if ($value) {
            $this->searchState['queryBuilder']->andWhere('a.visible = :state')
                ->setParameter('state', $value);
        }
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
        if ($this->searchState['searchMode'] !== static::SEARCH_MODE_COUNT) {
            list($sort, $order) = $this->getSortOrderValue($value);

            if ('productClass.position' === $sort) {
                $this->searchState['queryBuilder']->linkLeft('a.productClass');
            }
        }

        parent::prepareCndOrderBy($queryBuilder, $value);
    }
}
