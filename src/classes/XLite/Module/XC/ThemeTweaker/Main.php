<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker;

/**
 * Theme tweaker module main class
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
        return 'Theme tweaker';
    }

    /**
     * Module description
     *
     * @return string
     */
    public static function getDescription()
    {
        return 'This extension allows you to tweak your store design by adding and editing custom CSS styles and JavaScript code directly in the store back end. Also you can upload images to link them from your CSS styles.';
    }

    /**
     * Get theme files directory
     *
     * @return string
     */
    public static function getThemeDir()
    {
        return LC_DIR_VAR . 'theme' . LC_DS;
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

        return $list;
    }

    /**
     * Check target allowed
     *
     * @return boolean
     */
    public static function isTargetAllowed()
    {
        return !in_array(\XLite\Core\Request::getInstance()->target, ['image'], true);
    }

    /**
     * Check target allowed
     *
     * @return boolean
     */
    public static function isAdminTargetAllowed()
    {
        return in_array(\XLite\Core\Request::getInstance()->target, ['notification_editor'], true);
    }

    /**
     * Check user allowed
     *
     * @return boolean
     */
    public static function isUserAllowed()
    {
        $auth = \XLite\Core\Auth::getInstance();

        return $auth->getProfile()
               && $auth->getProfile()->isAdmin()
               && \XLite\Core\Auth::getInstance()->isPermissionAllowed(\XLite\Model\Role\Permission::ROOT_ACCESS);
    }

    /**
     * Decorator run this method at the end of cache rebuild
     *
     * @return void
     */
    public static function runBuildCacheHandler()
    {
        parent::runBuildCacheHandler();

        $overrides = \XLite\Core\Database::getRepo('XLite\Model\ViewList')->findOverridden();

        if ($overrides) {
            foreach ($overrides as $override) {
                $entity = \XLite\Core\Database::getRepo('XLite\Model\ViewList')->findEqual($override, true);

                if ($entity) {
                    $entity->mapOverrides($override);
                }
            }
        }
    }

    public static function getDumpOrder()
    {
        $orderId = \XLite\Core\TmpVars::getInstance()->themeTweakerDumpOrderId;
        $order = \XLite\Core\Database::getRepo('XLite\Model\Order')->find($orderId);

        if (null === $order) {
            $order = \XLite\Core\Database::getRepo('XLite\Model\Order')->findDumpOrder();
            if ($order) {
                \XLite\Core\TmpVars::getInstance()->themeTweakerDumpOrderId = $order->getOrderId();
            }
        }

        return $order;
    }

    public static function isOrderNotification($templateDirectory)
    {
        return in_array(
            $templateDirectory,
            [
                'order_advanced_changed',
                'order_canceled',
                'order_changed',
                'order_created',
                'order_failed',
                'order_processed',
                'order_shipped',
                'order_tracking_information',
            ],
            true
        );
    }
}
