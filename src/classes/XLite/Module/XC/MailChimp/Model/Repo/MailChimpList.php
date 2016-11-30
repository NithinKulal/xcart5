<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MailChimp\Model\Repo;

use XLite\Module\XC\MailChimp\Core;

/**
 * The MailChimpList model repository
 */
class MailChimpList extends \XLite\Model\Repo\ARepo
{
    const M_ENABLED = 'enabled';
    const M_REMOVED = 'removed';

    /**
     * Update MailChimp lists
     *
     * @param array $mailChimpLists MailChimp lists
     *
     * @return void
     */
    public function updateLists(array $mailChimpLists)
    {
        $savedMailChimpLists = $this->getAllMailChimpLists();
        $newListsIds = array();

        foreach ($mailChimpLists as $list) {
            $mailChimpList = $this->find($list['id']);

            if (is_null($mailChimpList)) {
                $mailChimpList = new \XLite\Module\XC\MailChimp\Model\MailChimpList();

                $mailChimpList->setId($list['id']);
                $mailChimpList->setDateCreated($list['date_created']);
                $mailChimpList->setSubscribeUrlShort($list['subscribe_url_short']);
                $mailChimpList->setSubscribeUrlLong($list['subscribe_url_long']);
            }

            $mailChimpList->setName($list['name']);
            $mailChimpList->setListRating($list['list_rating']);
            $mailChimpList->setMemberCount($list['stats']['member_count']);
            $mailChimpList->setOpenRate($list['stats']['open_rate']);
            $mailChimpList->setClickRate($list['stats']['click_rate']);

            $mailChimpList->setDateUpdated(\XLite\Core\Converter::time());

            $newListsIds[] = $mailChimpList->getId();

            \XLite\Core\Database::getEM()->persist($mailChimpList);
            $mailChimpList->update();

            $this->updateExistingListSegments($mailChimpList);
            $this->updateExistingListGroups($mailChimpList);

            \XLite\Core\Database::getEM()->flush();
        }

        foreach ($savedMailChimpLists as $mailChimpList) {
            if (!in_array($mailChimpList->getId(), $newListsIds)) {
                $mailChimpList->setEnabled(false);
                $mailChimpList->setIsRemoved(true);

                $mailChimpList->update();
            }
        }
    }

    /**
     * @param \XLite\Module\XC\MailChimp\Model\MailChimpList $mailChimpList
     */
    public function updateExistingListSegments(\XLite\Module\XC\MailChimp\Model\MailChimpList $mailChimpList)
    {
        $oldListSegments = $mailChimpList->getSegments();
        /** @var \XLite\Module\XC\MailChimp\Model\Repo\MailChimpSegment $segmentsRepo */
        $segmentsRepo = \XLite\Core\Database::getRepo('XLite\Module\XC\MailChimp\Model\MailChimpSegment');
        $segmentIds = $segmentsRepo->createNewSegments(
            Core\MailChimp::getInstance()->getSegments($mailChimpList->getId()),
            $mailChimpList
        );
        
        if (!empty($oldListSegments)) {
            foreach ($oldListSegments as $segment) {
                if (!in_array($segment->getId(), $segmentIds)) {
                    \XLite\Core\Database::getEM()->remove($segment);
                }
            }
        }
    }

    /**
     * @param \XLite\Module\XC\MailChimp\Model\MailChimpList $mailChimpList
     */
    public function updateExistingListGroups(\XLite\Module\XC\MailChimp\Model\MailChimpList $mailChimpList)
    {
        $oldListGroups = $mailChimpList->getGroups();
        /** @var \XLite\Module\XC\MailChimp\Model\Repo\MailChimpGroup $groupsRepo */
        $groupsRepo = \XLite\Core\Database::getRepo('XLite\Module\XC\MailChimp\Model\MailChimpGroup');
        $listGroupsIds = $groupsRepo->createNewGroups(
            Core\MailChimp::getInstance()->getGroups($mailChimpList->getId()),
            $mailChimpList
        );

        if (!empty($oldListGroups)) {
            foreach ($oldListGroups as $group) {
                if (!in_array($group->getId(), $listGroupsIds)) {
                    \XLite\Core\Database::getEM()->remove($group);
                }
            }
        }
    }

    /**
     * Get all MailChimp lists
     *
     * @param boolean $update Do update OPTIONAL
     *
     * @return \XLite\Module\XC\MailChimp\Model\MailChimpList[]
     */
    public function getAllMailChimpLists()
    {
        $cnd = new \XLite\Core\CommonCell();

        $return = $this->search($cnd);

        return $return;
    }

    /**
     * Get all active MailChimp lists
     *
     * @param boolean $update Do update OPTIONAL
     *
     * @return \XLite\Module\XC\MailChimp\Model\MailChimpList[]
     */
    public function getActiveMailChimpLists()
    {
        $cnd = new \XLite\Core\CommonCell();

        $cnd->{self::M_ENABLED} = true;

        $return = $this->search($cnd);

        return $return;
    }

    /**
     * Get number of MailChimp lists
     *
     * @return integer
     */
    public function countAllMailChimpLists()
    {
        $cnd = new \XLite\Core\CommonCell();

        return $this->search($cnd, true);
    }

    /**
     * Get number of MailChimp lists
     *
     * @return integer
     */
    public function countActiveMailChimpLists()
    {
        $cnd = new \XLite\Core\CommonCell();

        $cnd->{self::M_ENABLED} = true;

        return $this->search($cnd, true);
    }

    /**
     * Check if current MailChimp lists has removed list
     *
     * @return boolean
     */
    public function hasRemovedMailChimpLists()
    {
        $cnd = new \XLite\Core\CommonCell();

        $cnd->{self::M_REMOVED} = true;

        return $this->search($cnd, true) > 0 ? true : false;
    }

    /**
     * Check if provided profile is subscribes to provided list
     *
     * @param \XLite\Module\XC\MailChimp\Model\MailChimpList $list    MailChimp List
     * @param \XLite\Model\Profile                           $profile Profile
     *
     * @return boolean
     */
    public function isProfileSubscribed(\XLite\Module\XC\MailChimp\Model\MailChimpList $list, \XLite\Model\Profile $profile)
    {
        $count = $this->createPureQueryBuilder('l')
            ->select('COUNT(l.id)')
            ->innerJoin('l.profiles', 'p')
            ->andWhere('p.profile_id = :profile_id')
            ->andWhere('l.id = :list_id')
            ->setParameter('profile_id', $profile->getProfileId())
            ->setParameter('list_id', $list->getId())
            ->getSingleScalarResult();

        return $count > 0 ? true : false;
    }

    /**
     * Get default MailChimp list ID for select box preselected value
     *
     * @return string
     */
    public function getDefaultListId()
    {
        return $this->createPureQueryBuilder('l')
            ->select('l.id')
            ->andWhere('l.subscribeByDefault = :subscribeByDefault')
            ->setParameter('subscribeByDefault', 1)
            ->setMaxResults(1)
            ->getSingleScalarResult();
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
    protected function prepareCndRemoved(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        $queryBuilder->andWhere('m.isRemoved = :removed')
            ->setParameter('removed', $value);
    }
}
