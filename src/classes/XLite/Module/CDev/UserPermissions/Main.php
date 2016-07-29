<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\UserPermissions;

/**
 * User permissions module main class
 */
abstract class Main extends \XLite\Module\AModule
{
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
        return '1';
    }

    /**
     * Module name
     *
     * @return string
     */
    public static function getModuleName()
    {
        return 'User permissions';
    }

    /**
     * Module description
     *
     * @return string
     */
    public static function getDescription()
    {
        return 'Allows you to restrict access to backend functions to only those employees who need them. You can define administrator roles and configure which groups of back-end functions are available to users having these roles.';
    }

    /**
     * Decorator run this method at the end of cache rebuild
     *
     * @return void
     */
    public static function runBuildCacheHandler()
    {
        parent::runBuildCacheHandler();

        $enabledRole = \XLite\Core\Database::getRepo('XLite\Model\Role')->findOneBy(array('enabled' => true));
        if (!$enabledRole) {
            $permanent = \XLite\Core\Database::getRepo('XLite\Model\Role')->getPermanentRole();
            if (!$permanent) {
                $permanent = \XLite\Core\Database::getRepo('XLite\Model\Role')->findFrame(0, 1);
                $permanent = 0 < count($permanent) ? array_shift($permanent) : null;
            }

            if ($permanent) {
                $permanent->setEnabled(true);
                \XLite\Core\Database::getEM()->flush();
            }
        }
    }

    /**
     * Get permissions which should be enabled after enabling the module
     *
     * @return array
     */
    public static function getPermissions()
    {
        return parent::getCorePermissions();
    }
}
