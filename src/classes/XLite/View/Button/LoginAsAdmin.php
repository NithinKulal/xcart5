<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Button;

/**
 * 'Login as admin\vendor' button widget
 */
class LoginAsAdmin extends \XLite\View\Button\OperateAsThisUser
{
    /**
     * Get default label
     * todo: move translation here
     *
     * @return string
     */
    protected function getDefaultLabel()
    {
        return 'Log in as this admin';
    }

    /**
     * We make the full location path for the provided URL
     *
     * @return string
     */
    protected function getLocationURL()
    {
        return $this->buildURL('profile', 'loginAs', array(
            'profile_id' => $this->getProfile()->getProfileId()
        ));
    }

    /**
     * Return true if profile meets conditions
     *
     * @return boolean
     */
    protected function isProfileAllowed()
    {
        return $this->getProfile()
            && $this->getProfile()->isPersistent()
            && $this->getProfile()->isAdmin()
            && !$this->getProfile()->isPermissionAllowed(\XLite\Model\Role\Permission::ROOT_ACCESS)
            && $this->getProfile()->getProfileId() != \XLite\Core\Auth::getInstance()->getProfile()->getProfileId()
            && (\XLite\Core\Auth::getInstance()->isPermissionAllowed('manage admins')
                || \XLite\Core\Auth::getInstance()->isPermissionAllowed(\XLite\Model\Role\Permission::ROOT_ACCESS));
    }
}
