<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MailChimp\Model\Repo;

use XLite\Module\XC\MailChimp\Model;

/**
 * The Segment model repository
 */
class MailChimpSegment extends \XLite\Model\Repo\ARepo
{
    const M_ENABLED = 'enabled';
    const S_LIST    = 'list';

    /**
     * Check if provided profile is subscribes to provided list
     *
     * @param \XLite\Module\XC\MailChimp\Model\MailChimpSegment $segment MailChimp List
     * @param \XLite\Model\Profile                              $profile Profile
     *
     * @return boolean
     */
    public function isProfileSubscribed(\XLite\Module\XC\MailChimp\Model\MailChimpSegment $segment, \XLite\Model\Profile $profile)
    {
        $count = $this->createPureQueryBuilder('s')
            ->select('COUNT(s.id)')
            ->innerJoin('s.profiles', 'p')
            ->andWhere('p.profile_id = :profile_id')
            ->andWhere('s.id = :segment_id')
            ->setParameter('profile_id', $profile->getProfileId())
            ->setParameter('segment_id', $segment->getId())
            ->getSingleScalarResult();

        return $count > 0 ? true : false;
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param mixed                      $value        Condition data
     *
     * @return void
     */
    protected function prepareCndEnabled(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        $queryBuilder->andWhere('m.enabled = :enabled')
            ->setParameter('enabled', $value);
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param mixed                      $value        Condition data
     *
     * @return void
     */
    protected function prepareCndList(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        $queryBuilder->andWhere('m.list = :list_id')
            ->setParameter('list_id', $value);
    }

    /**
     * @param array         $segments
     * @param Model\MailChimpList $mailChimpList
     *
     * @return array
     */
    public function createNewSegments(array $segments, Model\MailChimpList $mailChimpList)
    {
        $segments = $this->prepareSegments($segments);

        $ids = array();

        if (!empty($segments)) {
            foreach ($segments as $segment) {

                $ids[] = $segment['id'];

                $listsSegment = $this->find($segment['id']);

                if (is_null($listsSegment)) {
                    $listsSegment = new \XLite\Module\XC\MailChimp\Model\MailChimpSegment();
                    $listsSegment->setId($segment['id']);
                    $listsSegment->setList($mailChimpList);
                }

                $listsSegment->setName($segment['name']);
                $listsSegment->setCreatedDate($segment['created_at']);
                $listsSegment->setStatic($segment['type'] === 'static');

                \XLite\Core\Database::getEM()->persist($listsSegment);
            }
        }

        return $ids;
    }

    /**
     * @param array $segments
     *
     * @return array
     */
    protected function prepareSegments(array $segments)
    {
        if (!empty($segments['static'])) {
            foreach ($segments['static'] as $i => $sg) {
                $segments['static'][$i]['is_static'] = true;
            }
        }

        if (!empty($segments['saved'])) {
            foreach ($segments['saved'] as $i => $sg) {
                $segments['saved'][$i]['is_static'] = false;
            }
        }

        return array_merge($segments['static'], $segments['saved']);
    }
}
