<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin\Base;

/**
 * AddonsList
 */
abstract class AddonsList extends \XLite\Controller\Admin\Base\Addon
{
    /**
     * Initialize controller
     *
     * @return void
     */
    public function init()
    {
        parent::init();

        if (!$this->getAction()) {

            // Check license keys registered in the store
            \XLite\Core\Marketplace::getInstance()->checkAddonsKeys();

            // Download data from marketplace
            \XLite\Core\Marketplace::getInstance()->getAddonsList();
        }
    }

    /**
     * Check if marketplace is accessible
     *
     * The admin is able to access the marketplate if:
     * 1) PHAR is installed on the server (the module packages can be installed to the shop)
     *
     * and
     *
     * 2) The marketplace is online and the cache is up-to-dated
     *
     * @return boolean
     */
    public function isMarketplaceAccessible()
    {
        // Check Phar availability and marketplace accessibility
        $result = extension_loaded('phar') && \XLite\Core\Marketplace::getInstance()->doTestMarketplace();

        if ($result) {
            // Check modules from marketplace is presented in the database
            $cnd = new \XLite\Core\CommonCell();
            $cnd->{\XLite\Model\Repo\Module::P_FROM_MARKETPLACE} = true;
            $countModules = \XLite\Core\Database::getRepo('XLite\Model\Module')->search($cnd, true);

            $result = 0 < $countModules;
        }

        return $result;
    }

    /**
     * Clean the installed module list (cleans during the logout)
     *
     * @return void
     */
    public static function cleanRecentlyInstalledModuleList()
    {
        \XLite\Core\Session::getInstance()->recently_installed_modules = array();
    }

    /**
     * Get the modules which were recently installed
     *
     * @return array
     */
    public static function getRecentlyInstalledModuleList()
    {
        return (array) \XLite\Core\Session::getInstance()->recently_installed_modules;
    }

    /**
     * Store the recently installed modules into the session
     *
     * @param array $installed
     *
     * @return void
     */
    public static function storeRecentlyInstalledModules($installed)
    {
        \XLite\Core\Session::getInstance()->recently_installed_modules = array_merge(
            static::getRecentlyInstalledModuleList(),
            $installed
        );
    }
}
