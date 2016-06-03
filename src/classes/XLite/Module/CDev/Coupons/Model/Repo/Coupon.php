<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Coupons\Model\Repo;

/**
 * Coupon repository
 */
class Coupon extends \XLite\Model\Repo\ARepo
{

    // {{{ Find duplicates

    /**
     * Find duplicates
     *
     * @param string                                  $code          Code
     * @param \XLite\Module\CDev\Coupons\Model\Coupon $currentCoupon Current coupon OPTIONAL
     *
     * @return array
     */
    public function findDuplicates($code, \XLite\Module\CDev\Coupons\Model\Coupon $currentCoupon = null)
    {
        return $this->defineFindDuplicatesQuery($code, $currentCoupon)->getResult();
    }

    /**
     * Define query for findDuplicates()
     *
     * @param string                                  $code          Code
     * @param \XLite\Module\CDev\Coupons\Model\Coupon $currentCoupon Current coupon OPTIONAL
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineFindDuplicatesQuery($code, \XLite\Module\CDev\Coupons\Model\Coupon $currentCoupon = null)
    {
        $queryBuilder = $this->createQueryBuilder('c')
            ->andWhere('COLLATE(c.code, utf8_bin) = :code')
            ->setParameter('code', $code);

        if ($currentCoupon) {
            $queryBuilder->andWhere('c.id != :cid')
                ->setParameter('cid', $currentCoupon->getId());
        }

        return $queryBuilder;
    }

    // }}}

    // {{{ Find by code

    /**
     * Find duplicates
     *
     * @param string $code Code
     *
     * @return array
     */
    public function findOneByCode($code)
    {
        return $this->defineFindOneByCode($code)->getSingleResult();
    }

    /**
     * Define query for findDuplicates()
     *
     * @param string $code Code
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineFindOneByCode($code)
    {
        $queryBuilder = $this->createQueryBuilder('c')
            ->andWhere('COLLATE(c.code, utf8_bin) = :code')
            ->setParameter('code', $code);

        return $queryBuilder;
    }

    // }}}
}
