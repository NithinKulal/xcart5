<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\VendorMessages\Model\Repo;

/**
 * Messages repository
 */
class Message extends \XLite\Model\Repo\ARepo
{

    /**
     * Count unread messages
     *
     * @param \XLite\Model\Profile|null $profile Profile
     *
     * @return integer
     */
    public function countUnread(\XLite\Model\Profile $profile = null)
    {
        $profile = $profile ?: \XLite\Core\Auth::getInstance()->getProfile();

        return count($this->defineCountUnreadQuery($profile)->getArrayResult());
    }

    /**
     * Count unread messages for own orders
     *
     * @param \XLite\Model\Profile|null $profile Profile
     *
     * @return integer
     */
    public function countOwnUnread(\XLite\Model\Profile $profile = null)
    {
        $profile = $profile ?: \XLite\Core\Auth::getInstance()->getProfile();

        return count($this->defineCountOwnUnreadQuery($profile)->getArrayResult());
    }

    /**
     * Define query for 'countUnread' method
     *
     * @param \XLite\Model\Profile $profile Profile
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineCountUnreadQuery(\XLite\Model\Profile $profile)
    {
        $qb = $this->createQueryBuilder('m')
            ->select('m.id')
            ->linkLeft('m.readers', 'r0', \Doctrine\ORM\Query\Expr\Join::WITH, 'r0.reader = :reader')
            ->linkLeft('m.readers', 'r1')
            ->groupBy('m.id')
            ->andHaving('COUNT(r1.id) != SUM(IF(r0.id IS NULL, 0, 1)) OR COUNT(r1.id) = 0')
            ->setParameter('reader', $profile);

        if (!$profile->isAdmin()) {
            $qb->linkInner('m.order', 'o')
                ->andWhere('o.orig_profile = :owner')
                ->setParameter('owner', $profile);
        }

        return $qb;
    }

    /**
     * Define query for 'countOwnUnread' method
     *
     * @param \XLite\Model\Profile $profile Profile
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineCountOwnUnreadQuery(\XLite\Model\Profile $profile)
    {
        return $this->createQueryBuilder('m')
            ->select('m.id')
            ->linkLeft('m.readers', 'r0', \Doctrine\ORM\Query\Expr\Join::WITH, 'r0.reader = :reader')
            ->linkLeft('m.readers', 'r1')
            ->linkInner('m.order', 'o')
            ->andWhere('o.orig_profile = :owner')
            ->groupBy('m.id')
            ->andHaving('COUNT(r1.id) != SUM(IF(r0.id IS NULL, 0, 1)) OR COUNT(r1.id) = 0')
            ->setParameter('owner', $profile)
            ->setParameter('reader', $profile);
    }

    /**
     * @inheritdoc
     */
    protected function performInsert($entity = null)
    {
        $entity = parent::performInsert($entity)->send();
        if ($entity->getReaders()->first()) {
            $this->getEntityManager()->persist($entity->getReaders()->first());
        }

        return $entity;
    }

}