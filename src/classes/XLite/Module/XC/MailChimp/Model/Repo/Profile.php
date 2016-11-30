<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MailChimp\Model\Repo;

/**
 * The Profile model repository
 */
abstract class Profile extends \XLite\Model\Repo\Profile implements \XLite\Base\IDecorator
{
    /**
     * Check if profile has any MailChimp subscriptions
     *
     * @param \XLite\Model\Profile $profile Profile
     *
     * @return boolean
     */
    public function hasMailChimpSubscriptions(\XLite\Model\Profile $profile)
    {
        $queryBuilder = $this->createPureQueryBuilder('p');

        $return = $queryBuilder->select('COUNT(DISTINCT mcl.id)')
            ->innerJoin('p.mail_chimp_lists', 'mcl')
            ->andWhere('p.profile_id = :profile_id')
            ->setParameter('profile_id', $profile->getProfileId())
            ->getSingleScalarResult();

        return $return > 0;
    }

    /**
     * Get the amount of orders placed during last 30 days
     *
     * @param \XLite\Model\Profile $profile Profile
     *
     * @return integer
     */
    public function countOrdersLastMonth(\XLite\Model\Profile $profile)
    {
        $cnd = new \XLite\Core\CommonCell();

        $cnd->{\XLite\Model\Repo\Order::P_DATE} = array(
            \XLite\Core\Converter::time() - 30 * 24 * 3600,
            \XLite\Core\Converter::time()
        );

        $cnd->{\XLite\Model\Repo\Order::P_PROFILE_ID} = $profile->getProfileId();

        return \XLite\Core\Database::getRepo('XLite\Model\Order')->search($cnd, true);
    }

    /**
     * Gt total amount of orders for this customer
     *
     * @param \XLite\Model\Profile $profile Profile
     *
     * @return float
     */
    public function getOrdersTotal(\XLite\Model\Profile $profile)
    {
        $queryBuilder = \XLite\Core\Database::getRepo('XLite\Model\Order');

        return $queryBuilder->createPureQueryBuilder('o')
            ->select('SUM(o.total)')
            ->andWhere('o.orig_profile = :profile_id')
            ->setParameter('profile_id', $profile->getProfileId())
            ->getSingleScalarResult();
    }

    /**
     * Check if profile membership matches segment conditions
     *
     * @param \XLite\Model\Profile                              $profile Profile
     * @param \XLite\Module\XC\MailChimp\Model\MailChimpSegment $segment Segment
     *
     * @return boolean
     */
    public function checkProfileMemberships(\XLite\Model\Profile $profile, \XLite\Module\XC\MailChimp\Model\MailChimpSegment $segment)
    {
        $queryBuilder = \XLite\Core\Database::getRepo('XLite\Module\XC\MailChimp\Model\MailChimpSegment')
            ->createPureQueryBuilder('s');

        $return = $queryBuilder->select('COUNT(DISTINCT s.id)')
            ->innerJoin('s.memberships', 'sm')
            ->andWhere('s.id = :segment_id')
            ->andWhere('sm.membership_id = :membership_id')
            ->setParameter('segment_id', $segment->getId())
            ->setParameter('membership_id', $profile->getMembershipId())
            ->getSingleScalarResult();

        return $return > 0;
    }

    /**
     * Check if customer ever purchased one of the products, specified in the segment condition
     *
     * @param \XLite\Model\Profile                              $profile Profile
     * @param \XLite\Module\XC\MailChimp\Model\MailChimpSegment $segment Segment
     *
     * @return boolean
     */
    public function checkProductsPurchased(\XLite\Model\Profile $profile, \XLite\Module\XC\MailChimp\Model\MailChimpSegment $segment)
    {
        $return = 0;

        $queryBuilder = \XLite\Core\Database::getRepo('XLite\Module\XC\MailChimp\Model\MailChimpSegment')
            ->createPureQueryBuilder('s');

        $segmentProducts = $queryBuilder->select('spr.product_id')
            ->innerJoin('s.products', 'spr')
            ->andWhere('s.id = :segment_id')
            ->setParameter('segment_id', $segment->getId())
            ->getArrayResult();

        $tmp = array();
        foreach ($segmentProducts as $pid) {
            $tmp[] = $pid['product_id'];
        }

        $segmentProducts = $tmp;

        if (!empty($segmentProducts)) {
            $queryBuilder = \XLite\Core\Database::getRepo('XLite\Model\Order')
                ->createPureQueryBuilder('o');

            $return = $queryBuilder->select('COUNT (DISTINCT o.order_id)')
                ->innerJoin('o.items', 'oi')
                ->andWhere('oi.object IN (\'' . implode('\',\'', $segmentProducts) .  '\')')
                ->andWhere('o.orig_profile = :profile_id')
                ->setParameter('profile_id', $profile->getProfileId())
                ->getSingleScalarResult();

            $return = $return > 0;
        }

        return $return;
    }
}
