<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\SimpleCMS;

/**
 * Simple CMS module main class
 */
abstract class Main extends \XLite\Module\AModule
{

    const SIMPLECMS_PERMISSION_MANAGE_CUSTOM_PAGES = 'manage custom pages';
    const SIMPLECMS_PERMISSION_MANAGE_MENUS = 'manage menus';

    /**
     * Author name
     *
     * @return string
     */
    public static function getAuthorName()
    {
        return 'X-Cart team';
    }

    /**
     * Get module major version
     *
     * @return string
     */
    public static function getMajorVersion()
    {
        return '5.3';
    }

    /**
     * Module version
     *
     * @return string
     */
    public static function getMinorVersion()
    {
        return '2';
    }

    /**
     * Get module build number (4th number in the version)
     *
     * @return string
     */
    public static function getBuildVersion()
    {
        return '3';
    }

    /**
     * Module name
     *
     * @return string
     */
    public static function getModuleName()
    {
        return 'Simple CMS';
    }

    /**
     * Module description
     *
     * @return string
     */
    public static function getDescription()
    {
        return 'Allows you to add a company logo and favicon, modify the primary website menu, edit the footer links and add custom website pages. When used with TinyMCE and Go Social modules, allows you to configure OpenGraph tags for your pages and edit their contents in the WYSIWYG mode. The module is incompatible with modules integrating your store with third-party CMS solutions.';
    }

    /**
     * Determines if we need to show settings form link
     *
     * @return boolean
     */
    public static function showSettingsForm()
    {
        return true;
    }

    /**
     * Return list of mutually exclusive modules
     *
     * @return array
     */
    public static function getMutualModulesList()
    {
        $list = parent::getMutualModulesList();
        $list[] = 'CDev\DrupalConnector';
        $list[] = 'SpurIT\SEConnector';
        $list[] = 'QSL\ExtendedSimpleCMS';

        return $list;
    }

    /**
     * Register permissions
     *
     * @return array
     */
    public static function getPermissions()
    {
        return array(
            static::SIMPLECMS_PERMISSION_MANAGE_CUSTOM_PAGES    => 'Manage custom pages',
            static::SIMPLECMS_PERMISSION_MANAGE_MENUS           => 'Manage menus',
        );
    }

    /**
     * Method to call just before the module is disabled via core
     *
     * @return void
     */
    public static function callDisableEvent()
    {
        parent::callDisableEvent();

        \XLite\Core\Database::getRepo('XLite\Module\CDev\SimpleCMS\Model\Menu')->deleteRootMenu();
    }
}
