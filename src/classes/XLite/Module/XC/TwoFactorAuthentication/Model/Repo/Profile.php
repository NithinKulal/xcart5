<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\TwoFactorAuthentication\Model\Repo;

/**
 * The Profile model repository
 */
class Profile extends \XLite\Model\Repo\Profile implements \XLite\Base\IDecorator
{
    /**
     * Set NULL authy_id field for all entities
     *
     * @return void
     */
    public function clearAuthyIds()
    {
        $qb = $this->getQueryBuilder();
        $qb->update($this->_entityName, 'p')
            ->set('p.authy_id', 'NULL');
        $qb->execute();
    }

    /**
     * Set NULL authy_id field for profile entity by profile_id
     *
     * @param integer $profileId Profile id
     *
     * @return void
     */
    public function clearAuthyIdById($profileId)
    {
        $qb = $this->getQueryBuilder();
        $qb->update($this->_entityName, 'p')
            ->set('p.authy_id', 'NULL')
            ->where('p.profile_id=:profile_id')
            ->setParameter('profile_id', $profileId);
        $qb->execute();
    }
}