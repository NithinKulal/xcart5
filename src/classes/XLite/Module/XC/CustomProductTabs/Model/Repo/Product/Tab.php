<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CustomProductTabs\Model\Repo\Product;

/**
 * Product tabs repository
 */
class Tab extends \XLite\Model\Repo\Base\I18n
{
    /**
     * Allowable search params
     */
    const SEARCH_PRODUCT         = 'product';
    const P_POSITION             = 'position';

    // {{{ Search

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param mixed                      $value        Condition data
     *
     * @return void
     */
    protected function prepareCndProduct(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        if ($value && is_object($value)) {
            $queryBuilder->andWhere('t.product = :product')
                ->setParameter('product', $value)
                ->orderBy('t.position');
        }
    }


    // }}}

}