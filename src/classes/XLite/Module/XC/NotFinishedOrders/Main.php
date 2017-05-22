<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\NotFinishedOrders;

/**
 * Main module
 */
abstract class Main extends \XLite\Module\AModule
{
    /**
     * Constants: Create NFO modes
     */
    const NFO_MODE_ON_FAILURE = 'onFailure';
    const NFO_MODE_ON_PLACE   = 'onPlaceOrder';

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
     * Module name
     *
     * @return string
     */
    public static function getModuleName()
    {
        return 'Not Finished Orders';
    }

    /**
     * Module description
     *
     * @return string
     */
    public static function getDescription()
    {
        return 'This addon allows you to track not finished orders and contact customers, who have failed to proceed through the payment process.';
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
        return '2';
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
     * Return true if NFO must be created on payment failure
     *
     * @return boolean
     */
    public static function isCreateOnFailure()
    {
        return static::NFO_MODE_ON_FAILURE == \XLite\Core\Config::getInstance()->XC->NotFinishedOrders->create_nfo_mode;
    }

    /**
     * Return true if NFO must be created on place order
     *
     * @return boolean
     */
    public static function isCreateOnPlaceOrder()
    {
        return static::NFO_MODE_ON_PLACE == \XLite\Core\Config::getInstance()->XC->NotFinishedOrders->create_nfo_mode;
    }
}
