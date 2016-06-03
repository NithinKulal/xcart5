<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * X-Cart
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the software license agreement
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.x-cart.com/license-agreement.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to licensing@x-cart.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not modify this file if you wish to upgrade X-Cart to newer versions
 * in the future. If you wish to customize X-Cart for your needs please
 * refer to http://www.x-cart.com/ for more information.
 *
 * @category  X-Cart 5
 * @author    Qualiteam software Ltd <info@x-cart.com>
 * @copyright Copyright (c) 2011-2014 Qualiteam software Ltd <info@x-cart.com>. All rights reserved
 * @license   http://www.x-cart.com/license-agreement.html X-Cart 5 License Agreement
 * @link      http://www.x-cart.com/
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
        return '5.2';
    }

    /**
     * Module version
     *
     * @return string
     */
    public static function getMinorVersion()
    {
        return '7';
    }

    /**
     * 5.2.14 version is required for the current module
     * 
     * @return string
     */
    public static function getMinorRequiredCoreVersion()
    {
        return '14';
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
