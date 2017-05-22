<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\QSL\CloudSearch;

use Includes\Utils\URLManager;
use XLite\Core\Config;
use XLite\Module\QSL\CloudSearch\Core\RegistrationScheduler;
use XLite\Module\QSL\CloudSearch\Core\ServiceApiClient;

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
        return 'CloudSearch & CloudFilters';
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
        return '6';
    }

    /**
     * Get module build number (4th number in the version)
     *
     * @return string
     */
    public static function getBuildVersion()
    {
        return '5';
    }

    /**
     * 5.3.1 version is required for the module
     *
     * @return string
     */
    public static function getMinorRequiredCoreVersion()
    {
        return '1';
    }

    /**
     * Module description
     *
     * @return string
     */
    public static function getDescription()
    {
        return 'The module provides integration with both CloudSearch and CloudFilters services. CloudSearch integrates with X-Cart 5 to enable dynamic, real-time product search with highly relevant search results. CloudFilters works on top of CloudSearch to enable layered navigation and faceted search for X-Cart stores. Power up your store with enterprise-class search and navigation technologies for better conversion!';
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
        $list   = parent::getMutualModulesList();
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
     * Check if CloudSearch is configured
     *
     * @return boolean
     */
    public static function isConfigured()
    {
        $apiClient = new ServiceApiClient();

        $apiKey    = $apiClient->getApiKey();
        $secretKey = $apiClient->getSecretKey();

        return !empty($apiKey) && !empty($secretKey);
    }

    /**
     * Check if CloudFilters is enabled
     *
     * @return boolean
     */
    public static function isCloudFiltersEnabled()
    {
        return Config::getInstance()->QSL->CloudSearch->isCloudFiltersEnabled;
    }

    /**
     * Check if store is set up in multi-domain mode.
     * In multi-domain mode only the main domain will be registered in CS and all links will be indexed
     * as absolute URLs without host name so that every domain can use them properly.
     *
     * @return bool
     */
    public static function isMultiDomain()
    {
        $domains = array_filter(URLManager::getShopDomains());

        return count($domains) > 1;
    }
}