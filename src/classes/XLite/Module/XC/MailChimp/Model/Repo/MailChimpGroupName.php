<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MailChimp\Model\Repo;

use XLite\Module\XC\MailChimp\Model;

/**
 * The Group name model repository
 */
class MailChimpGroupName extends \XLite\Model\Repo\ARepo
{
    /**
     * @param array          $names
     * @param Model\MailChimpGroup $group
     *
     * @return array
     */
    public function createNewGroupNames(array $names, Model\MailChimpGroup $group)
    {
        $ids = array();

        if (!empty($names)) {
            foreach ($names as $name) {

                $ids[] = $name['id'];

                $object = $this->find($name['id']);

                if (is_null($object)) {
                    $object = new \XLite\Module\XC\MailChimp\Model\MailChimpGroupName();
                    $object->setId($name['id']);
                    $object->setGroup($group);
                }
                $object->setName($name['name']);
                $object->setSubscribersCount($name['subscriber_count']);

                \XLite\Core\Database::getEM()->persist($object);
            }
        }

        return $ids;
    }

    /**
     * @param \XLite\Module\XC\MailChimp\Model\MailChimpGroupName   $groupName      MailChimp List group name
     * @param \XLite\Model\Profile                                  $profile        Profile
     *
     * @return boolean
     */
    public function isProfileChecked(\XLite\Module\XC\MailChimp\Model\MailChimpGroupName $groupName, \XLite\Model\Profile $profile)
    {
        $count = $this->createPureQueryBuilder('gn')
            ->select('COUNT(gn.id)')
            ->innerJoin('gn.profiles', 'p')
            ->andWhere('p.profile_id = :profile_id')
            ->andWhere('gn.id = :name_id')
            ->setParameter('profile_id', $profile->getProfileId())
            ->setParameter('name_id', $groupName->getId())
            ->getSingleScalarResult();

        return $count > 0 ? true : false;
    }

}
