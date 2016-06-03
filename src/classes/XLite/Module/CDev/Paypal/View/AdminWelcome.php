<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\View;

/**
 * Paypal banner
 *
 * @ListChild (list="dashboard-center", zone="admin", weight="20")
 */
class AdminWelcome extends \XLite\View\Dialog
{
    /**
     * Add widget specific JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'main/controller.js';

        return $list;
    }

    /**
     * Add widget specific CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'main/style.css';
        $list[] = $this->getDir() . '/style.css';

        return $list;
    }

    /**
     * Return templates directory name
     *
     * @return string
     */
    protected function getDir()
    {
        return 'modules/CDev/Paypal/welcome';
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        $config = \XLite\Core\Config::getInstance()->CDev->Paypal;

        return parent::isVisible()
            && $this->isRootAccess()
            && 1 != \XLite\Core\Session::getInstance()->hide_welcome_block_paypal
            && 'Y' == $config->show_admin_welcome;
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
     * Get box class
     *
     * @return string
     */
    protected function getBoxClass()
    {
        return 'admin-welcome paypal';
    }

    /**
     * Returns paupal email
     *
     * @return string
     */
    protected function getPaypalEmail()
    {
        $method = \XLite\Module\CDev\Paypal\Main::getPaymentMethod(\XLite\Module\CDev\Paypal\Main::PP_METHOD_EC);

        return $method->getSetting('email');
    }
}
