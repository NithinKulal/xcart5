<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\AuctionInc\Model\Repo\Shipping;

/**
 * Shipping method model
 */
class Method extends \XLite\Model\Repo\Shipping\Method implements \XLite\Base\IDecorator
{
    /**
     * Search parameters
     */
    const P_AUCTION_INC_FILTER  = 'auctionIncFilter';

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param array                      $value        Filter array
     *
     * @return void
     */
    protected function prepareCndAuctionIncFilter(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        if ($value && is_array($value)) {
            $i = 0;
            foreach ($value as $filter) {
                $filterValueName = 'auctionIncFilter' . $i++;
                $queryBuilder->andWhere($queryBuilder->expr()->notLike('m.code', ':' . $filterValueName))
                    ->setParameter($filterValueName, $filter);
            }
        }
    }

    /**
     * @return boolean
     */
    public function isAuctionIncEnabled()
    {
        $name = 'auctionInc';
        $qb = $this->createPureQueryBuilder('m');

        $qb->select('m.enabled')
            ->andWhere('m.carrier = :carrier')
            ->andWhere('m.processor = :processor')
            ->setParameter('carrier', '')
            ->setParameter('processor', $name);

        return (bool) $qb->getSingleScalarResult();
    }
}
