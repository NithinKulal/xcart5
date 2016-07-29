<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\QSL\CloudSearch;

use XLite\Module\QSL\CloudSearch\Core\RegistrationScheduler;

/**
 * Featured Products module manager
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
        return 'Qualiteam';
    }

    /**
     * Module name
     *
     * @return string
     */
    public static function getModuleName()
    {
        return 'CloudSearch';
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
     * 5.2.14 version is required for the current module
     * 
     * @return string
     */
    public static function getMinorRequiredCoreVersion()
    {
        return '0';
    }

    /**
     * Module description
     *
     * @return string
     */
    public static function getDescription()
    {
        return 'CloudSearch is a SaaS solution that integrates with X-Cart 5 to enable dynamic, real-time product search with highly relevant search results. Power up your store with enterprise-class search technologies for better conversion!';
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
     * Return link to the module page
     *
     * @return string
     */
    public static function getPageURL()
    {
        return \XLite::getXCartURL('http://www.x-cart.com/extensions/addons/cloudsearch.html');
    }

    /**
     * Return list of mutually exclusive modules
     *
     * @return array
     */
    public static function getMutualModulesList()
    {
        $list = parent::getMutualModulesList();
        $list[] = 'CDev\InstantSearch';

        return $list;
    }

    /**
     * Decorator run this method at the end of cache rebuild
     *
     * @return void
     */
    public static function runBuildCacheHandler()
    {
        parent::runBuildCacheHandler();

        RegistrationScheduler::getInstance()->schedule();
    }
    
    /**
     * Check if the CloudSearch specific search is used
     * 
     * @return boolean
     */
    public static function doSearch()
    {
        return \XLite\Core\Config::getInstance()->QSL->CloudSearch->doSearch;
    }
}
