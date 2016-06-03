<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\SalesTax\Model\Repo\Tax;

/**
 * Tax rate repository
 */
class Rate extends \XLite\Model\Repo\ARepo
{
    /**
     * Search params
     */
    const PARAM_TAXABLE_BASE      = 'taxableBase';
    const PARAM_EXCL_TAXABLE_BASE = 'excludeTaxableBase';

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $qb    Query builder to prepare
     * @param mixed                      $value Condition data
     *
     * @return void
     */
    protected function prepareCndTaxableBase(\Doctrine\ORM\QueryBuilder $qb, $value)
    {
        $qb->andWhere('r.taxableBase = :taxableBase')
            ->setParameter('taxableBase', $value);
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $qb    Query builder to prepare
     * @param mixed                      $value Condition data
     *
     * @return void
     */
    protected function prepareCndExcludeTaxableBase(\Doctrine\ORM\QueryBuilder $qb, $value)
    {
        $list = (!is_array($value) ? array($value) : $value);

        foreach ($list as $k => $val) {
            if (empty($val)) {
                unset($list[$k]);
            }
        }

        if (!empty($list)) {
            if (1 == count($list)) {
                $qb->andWhere('r.taxableBase != :taxableBase')
                    ->setParameter('taxableBase', $list[0]);

            } else {
                $qb->andWhere('r.taxableBase NOT IN (:taxableBase)')
                    ->setParameter('taxableBase', $list);
            }
        }
    }
}
