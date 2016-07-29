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
        return '1';
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
        return !in_array(\XLite\Core\Request::getInstance()->target, array('image'));
    }

    /**
     * Check user allowed
     *
     * @return boolean
     */
    public static function isUserAllowed()
    {
        $auth = \XLite\Core\Auth::getInstance();

        return $auth->getProfile() && $auth->getProfile()->isAdmin();
    }

    /**
     * Decorator run this method at the end of cache rebuild
     *
     * @return void
     */
    public static function runBuildCacheHandler()
    {
        parent::runBuildCacheHandler();

        $overrides = \XLite\Core\Database::getRepo('\XLite\Model\ViewList')->findOverridden();

        if ($overrides) {
            foreach ($overrides as $override) {
                $entity = \XLite\Core\Database::getRepo('\XLite\Model\ViewList')->findEqual($override, true);

                if ($entity) {
                    $entity->mapOverrides($override);
                }
            }
        }
    }
}
