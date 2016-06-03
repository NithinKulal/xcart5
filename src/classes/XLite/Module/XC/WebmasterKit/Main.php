<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\WebmasterKit;

use XLite\Core\Config;
use XLite\Module\XC\WebmasterKit\Logic\DebugBarSettingsManager;

/**
 * Dev module main class
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
        return '0';
    }

    /**
     * Module name
     *
     * @return string
     */
    public static function getModuleName()
    {
        return 'Webmaster Kit';
    }

    /**
     * Module description
     *
     * @return string
     */
    public static function getDescription()
    {
        return 'This module helps web developers. It is used to find specific templates (the same as Webmaster mode functionality in X-Cart 4), SQL logging, benchmarking.';
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
     * Method to initialize concrete module instance
     *
     * @return void
     */
    public static function init()
    {
        parent::init();

        static::registerLibAutoloader();

        $settingsMgr = new DebugBarSettingsManager();
        if (Config::getInstance()->XC->WebmasterKit->debugBarEnabled
            && $settingsMgr->areWidgetsTabEnabled()
        ) {
            static::getEventDispatcher()->addSubscriber(new EventListener\WidgetRenderSubscriber());
        }
    }

    /**
     * Register lib autoloader
     *
     * @return void
     */
    protected static function registerLibAutoloader()
    {
        require_once(static::getLibDirectoryPath() . LC_DS . 'vendor' . LC_DS . 'autoload.php');
    }

    /**
     * Absolute path to libs
     *
     * @return string
     */
    protected static function getLibDirectoryPath()
    {
        return LC_DIR_MODULES . 'XC' . LC_DS . 'WebmasterKit' . LC_DS . 'lib';
    }
}
