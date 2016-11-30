<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MailChimp\Model\Repo;

use XLite\Module\XC\MailChimp\Core;
use XLite\Module\XC\MailChimp\Model;

/**
 * The Group model repository
 */
class MailChimpGroup extends \XLite\Model\Repo\ARepo
{
    /**
     * @param array         $groups
     * @param Model\MailChimpList $mailChimpList
     *
     * @return array
     */
    public function createNewGroups(array $groups, Model\MailChimpList $mailChimpList)
    {
        $ids = array();

        if (!empty($groups)) {
            foreach ($groups as $group) {

                $ids[] = $group['id'];

                $listGroup = $this->find($group['id']);


                if (is_null($listGroup)) {
                    $listGroup = new \XLite\Module\XC\MailChimp\Model\MailChimpGroup();
                    $listGroup->setId($group['id']);
                    $listGroup->setList($mailChimpList);
                }
                $listGroup->setTitle($group['title']);
                $listGroup->setType($group['type']);

                $oldListGroupsNames = $listGroup->getNames();
                /** @var \XLite\Module\XC\MailChimp\Model\Repo\MailChimpGroupName $namesRepo */
                $namesRepo = \XLite\Core\Database::getRepo('XLite\Module\XC\MailChimp\Model\MailChimpGroupName');
                $listGroupsNamesIds = $namesRepo->createNewGroupNames(
                        Core\MailChimp::getInstance()->getGroupNames($mailChimpList->getId(), $group['id']),
                        $listGroup
                    );

                \XLite\Core\Database::getEM()->persist($listGroup);

                if (!empty($oldListGroupsNames)) {
                    foreach ($oldListGroupsNames as $name) {
                        if (!in_array($name->getId(), $listGroupsNamesIds)) {
                            \XLite\Core\Database::getEM()->remove($name);
                        }
                    }
                }
            }
        }

        return $ids;
    }

}
