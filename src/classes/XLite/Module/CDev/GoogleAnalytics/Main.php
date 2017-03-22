<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\GoogleAnalytics;
use XLite\Module\CDev\GoogleAnalytics\Logic\ActionsStorage;
use XLite\Module\CDev\GoogleAnalytics\Logic\Action;

/**
 * Module class
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
        return 'Google Analytics';
    }

    /**
     * Module description
     *
     * @return string
     */
    public static function getDescription()
    {
        return 'Enables tracking and analyzing your website e-commerce statistics with Google Analytics.';
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
     * @inheritdoc
     */
    public static function init()
    {
        parent::init();

        ActionsStorage::getInstance()->addAction(
            'purchaseAction',
            new Action\Purchase()
        );

        ActionsStorage::getInstance()->addAction(
            'checkoutEnteredAction',
            new Action\CheckoutInit()
        );
    }

    /**
     * @return bool
     */
    public static function useUniversalAnalytics()
    {
        return \XLite\Core\Config::getInstance()->CDev->GoogleAnalytics->ga_account
            && 'U' === \XLite\Core\Config::getInstance()->CDev->GoogleAnalytics->ga_code_version;
    }

    /**
     * @return bool
     */
    public static function isECommerceEnabled()
    {
        return static::useUniversalAnalytics()
            && \XLite\Core\Config::getInstance()->CDev->GoogleAnalytics->ecommerce_enabled;
    }

    /**
     * @return bool
     */
    public static function isPurchaseImmediatelyOnSuccess()
    {
        return !\XLite\Core\Config::getInstance()->CDev->GoogleAnalytics->purchase_only_on_paid;
    }

    /**
     * @return bool
     */
    public static function isDebugMode()
    {
        return \XLite\Core\Config::getInstance()->CDev->GoogleAnalytics->debug_mode;
    }
}
