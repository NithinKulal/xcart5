<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */


namespace XLite\Module\XC\NextPreviousProduct\Model\Repo;


abstract class Product extends \XLite\Model\Repo\Product implements \XLite\Base\IDecorator
{
    /**
     * Search only ids
     *
     * @param \XLite\Core\CommonCell $cnd       Search condition
     *
     * @return \Doctrine\ORM\PersistentCollection|integer
     */
    public function searchOnlyIds(\XLite\Core\CommonCell $cnd)
    {
        $this->searchState['queryBuilder'] = $this->createQueryBuilder();

        $this->searchState['currentSearchCnd'] = $cnd;

        foreach ($this->searchState['currentSearchCnd'] as $key => $value) {
            $this->callSearchConditionHandler($value, $key);
        }

        return $this->searchIdsResult($this->searchState['queryBuilder']);
    }

    /**
     * Search ids routine.
     *
     * @param \Doctrine\ORM\QueryBuilder $qb Query builder routine
     *
     * @return \Doctrine\ORM\PersistentCollection|integer
     */
    public function searchIdsResult(\Doctrine\ORM\QueryBuilder $qb)
    {
        $qb->select('p.product_id')
            ->orderBy('p.product_id')
            ->groupBy('p.product_id');

        return $qb->getQuery()->getScalarResult();
    }
}