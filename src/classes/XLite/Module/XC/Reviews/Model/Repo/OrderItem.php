<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Reviews\Model\Repo;

/**
 * The OrderItem model repository extension
 */
class OrderItem extends \XLite\Model\Repo\OrderItem implements \XLite\Base\IDecorator
{
    /**
     * Returns the top sellers count (used on the dashboard)
     *
     * @param integer              $productId Product Id
     * @param \XLite\Model\Profile $profile   Customer profile
     *
     * @return boolean
     */
    public function countItemsPurchasedByCustomer($productId, $profile)
    {
        return 0 < $this->defineCountItemsPurchasedByCustomer($productId, $profile)->getSingleScalarResult();
    }

    /**
     * Prepare query for countItemsPurchasedByCustomer() method
     *
     * @param integer              $productId Product Id
     * @param \XLite\Model\Profile $profile   Customer profile
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineCountItemsPurchasedByCustomer($productId, $profile)
    {
        $qb = $this->createQueryBuilder('i');

        $qb->select('COUNT(i.item_id)')
            ->innerJoin('i.object', 'p')
            ->innerJoin('i.order', 'o')
            ->innerJoin('o.orig_profile', 'profile')
            ->innerJoin('o.paymentStatus', 'ps')
            ->andWhere('p.product_id = :productId')
            ->andWhere('profile.profile_id = :profileId')
            ->andWhere($qb->expr()->in('ps.code', \XLite\Model\Order\Status\Payment::getPaidStatuses()))
            ->setParameter('productId', $productId)
            ->setParameter('profileId', $profile->getProfileId());

        return $qb;
    }
}
