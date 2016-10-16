<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\Amazon\PayWithAmazon;

/**
 * PayWithAmazon module main class
 */
abstract class Main extends \XLite\Module\AModule
{
    protected static $api;

    /**
     * Author name
     *
     * @return string
     */
    public static function getAuthorName()
    {
        return 'Amazon Payments';
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
     * Module name
     *
     * @return string
     */
    public static function getModuleName()
    {
        return 'Pay with Amazon';
    }

    /**
     * Module description
     *
     * @return string
     */
    public static function getDescription()
    {
        return 'This module enables Pay with Amazon functionality';
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
     * Return link to settings form
     *
     * @return string
     */
    public static function getSettingsForm()
    {
        return \XLite\Core\Converter::buildURL('pay_with_amazon');
    }

    /**
     * @return AMZ
     */
    public static function getApi()
    {
        if (null === static::$api) {
            static::$api = new AMZ(\XLite\Core\Config::getInstance()->Amazon->PayWithAmazon);
        }

        return static::$api;
    }
}
