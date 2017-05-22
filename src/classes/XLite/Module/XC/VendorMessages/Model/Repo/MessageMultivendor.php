<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\VendorMessages\Model\Repo;

/**
 * Messages repository
 *
 * @Decorator\After ("XC\VendorMessages")
 * @Decorator\Depend ("XC\MultiVendor")
 */
class MessageMultivendor extends \XLite\Module\XC\VendorMessages\Model\Repo\Message implements \XLite\Base\IDecorator
{
    /**
     * Count unread messages (for admin)
     *
     * @param \XLite\Model\Profile|null $profile Profile
     *
     * @return integer
     */
    public function countUnreadForAdmin(\XLite\Model\Profile $profile = null)
    {
        $profile = $profile ?: \XLite\Core\Auth::getInstance()->getProfile();

        return count($this->defineCountUnreadForAdminQuery($profile)->getArrayResult());
    }

    /**
     * Count unread messages (for vendor)
     *
     * @param \XLite\Model\Profile|null $profile Profile
     *
     * @return integer
     */
    public function countUnreadForVendor(\XLite\Model\Profile $profile = null)
    {
        $profile = $profile ?: \XLite\Core\Auth::getInstance()->getProfile();

        return count($this->defineCountUnreadForVendorQuery($profile)->getArrayResult());
    }

    /**
     * Count vendor's messages
     *
     * @param \XLite\Model\Profile $profile Vendor profile OPTIONAL
     *
     * @return integer
     */
    public function countByVendor(\XLite\Model\Profile $profile = null)
    {
        $profile = $profile ?: \XLite\Core\Auth::getInstance()->getProfile();

        return $this->defineCountByVendorQuery($profile)->count();
    }

    /**
     * Count opened disputes
     *
     * @return integer
     */
    public function countDisputes()
    {
        return count($this->defineCountDisputesQuery()->getArrayResult());
    }

    /**
     * Find last messages which open dispute
     *
     * @param \XLite\Model\Order $order Order
     *
     * @return \XLite\Module\XC\VendorMessages\Model\Message
     */
    public function findOneLastOpenDispute(\XLite\Model\Order $order)
    {
        return $this->defineFindOneLastOpenDisputeQuery($order)->getSingleResult();
    }

    /**
     * Define query for 'countUnreadForAdmin' method
     *
     * @param \XLite\Model\Profile $profile Profile
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineCountUnreadForAdminQuery(\XLite\Model\Profile $profile)
    {
        return $this->createQueryBuilder('m')
            ->select('m.id')
            ->linkLeft('m.readers', 'r0', \Doctrine\ORM\Query\Expr\Join::WITH, 'r0.reader = :reader')
            ->linkLeft('m.readers', 'r1')
            ->linkInner('m.order', 'o')
            ->andWhere('o.is_opened_dispute = :enabled_dispute OR o.is_watch_messages = :enabled_watch OR o.vendor IS NULL')
            ->groupBy('m.id')
            ->andHaving('COUNT(r1.id) != SUM(IF(r0.id IS NULL, 0, 1)) OR COUNT(r1.id) = 0')
            ->setParameter('reader', $profile)
            ->setParameter('enabled_dispute', true)
            ->setParameter('enabled_watch', true);
    }

    /**
     * Define query for 'countDisputes' method
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineCountDisputesQuery()
    {
        return $this->createQueryBuilder('m')
            ->select('o.order_id')
            ->linkInner('m.order', 'o')
            ->andWhere('o.is_opened_dispute = :enabled_dispute')
            ->setParameter('enabled_dispute', true)
            ->groupBy('o.order_id');
    }

    /**
     * Define query for 'countUnreadForVendor' method
     *
     * @param \XLite\Model\Profile $profile Profile
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineCountUnreadForVendorQuery(\XLite\Model\Profile $profile)
    {
        return $this->createQueryBuilder('m')
            ->select('m.id')
            ->linkLeft('m.readers', 'r0', \Doctrine\ORM\Query\Expr\Join::WITH, 'r0.reader = :reader')
            ->linkLeft('m.readers', 'r1')
            ->linkInner('m.order', 'o')
            ->andWhere('o.vendor = :vendor')
            ->groupBy('m.id')
            ->andHaving('COUNT(r1.id) != SUM(IF(r0.id IS NULL, 0, 1)) OR COUNT(r1.id) = 0')
            ->setParameter('reader', $profile)
            ->setParameter('vendor', $profile);
    }

    /**
     * Define query for 'countByVendor' method
     *
     * @param \XLite\Model\Profile $profile Profile
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineCountByVendorQuery(\XLite\Model\Profile $profile)
    {
        return $this->createQueryBuilder('m')
            ->linkInner('m.order', 'o')
            ->andWhere('o.vendor = :vendor')
            ->setParameter('vendor', $profile);
    }

    /**
     * Define query for 'findOneLastOpenDispute' method
     *
     * @param \XLite\Model\Order $order Order
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineFindOneLastOpenDisputeQuery(\XLite\Model\Order $order)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.order = :order AND m.dispute_state = :open')
            ->setParameter('order', $order)
            ->setParameter('open', \XLite\Module\XC\VendorMessages\Model\Message::DISPUTE_STATE_OPEN)
            ->orderBy('m.date', 'DESC')
            ->setMaxResults(1);
    }

}