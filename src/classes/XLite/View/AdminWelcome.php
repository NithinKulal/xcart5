<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

/**
 * Admin's 'Welcome...' block widget
 *
 * @ListChild (list="dashboard-center", zone="admin", weight="50")
 */
class AdminWelcome extends \XLite\View\Dialog
{
    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        return array_merge(parent::getAllowedTargets(), array('main'));
    }

    /**
     * Add widget specific CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = $this->getDir() . '/style.css';

        return $list;
    }

    /**
     * Add widget specific JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = $this->getDir() . '/controller.js';

        return $list;
    }

    /**
     * Return templates directory name
     *
     * @return string
     */
    protected function getDir()
    {
        return 'main';
    }

    /**
     * Check block visibility
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return $this->isRootAccess() ? $this->isAdminWelcomeBlockVisible() : true;
    }

    /**
     * Check if the current admin user has the root access
     *
     * @return boolean
     */
    protected function isRootAccess()
    {
        return \XLite\Core\Auth::getInstance()->isPermissionAllowed(\XLite\Model\Role\Permission::ROOT_ACCESS);
    }

    /**
     * Return the roles of the current admin user
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    protected function getRoles()
    {
        return \XLite\Core\Auth::getInstance()->getProfile()->getRoles();
    }

    /**
     * Get box class
     *
     * @return string
     */
    protected function getBoxClass()
    {
        return 'admin-welcome' . ($this->isRootAccess() ? ' root' : ' non-root');
    }
}

