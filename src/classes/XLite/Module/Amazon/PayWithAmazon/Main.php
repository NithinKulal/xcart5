<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\Amazon\PayWithAmazon;

use XLite\Core\Cache\ExecuteCached;

/**
 * PayWithAmazon module main class
 */
abstract class Main extends \XLite\Module\AModule
{
    const PLATFORM_ID = 'A1PQFSSKP8TT2U';

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
        return '4';
    }

    /**
     * Get module build number (4th number in the version)
     *
     * @return string
     */
    public static function getBuildVersion()
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
     * The module is defined as the payment module
     *
     * @return integer|null
     */
    public static function getModuleType()
    {
        return static::MODULE_TYPE_PAYMENT;
    }

    /**
     * Determines if we need to show settings form link
     *
     * @return boolean
     */
    public static function showSettingsForm()
    {
        return false;
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

    public static function log($message)
    {
        \XLite\Logger::logCustom('amazon_pa', $message);
    }

    /**
     * @return Object|\XLite\Model\Payment\Method
     */
    public static function getMethod()
    {
        return ExecuteCached::executeCachedRuntime(function () {
            return \XLite\Core\Database::getRepo('XLite\Model\Payment\Method')
                ->findOneBy(['service_name' => 'PayWithAmazon']);
        }, [__CLASS__, __FUNCTION__]);
    }

    /**
     * @return \XLite\Module\Amazon\PayWithAmazon\Model\Payment\Processor\PayWithAmazon
     */
    public static function getProcessor()
    {
        return static::getMethod()->getProcessor();
    }

    /**
     * @return \PayWithAmazon\Client
     */
    public static function getClient()
    {
        return ExecuteCached::executeCachedRuntime(function () {
            $method    = static::getMethod();
            $processor = static::getProcessor();
            //$config       = \XLite\Core\Config::getInstance()->Amazon->PayWithAmazon;
            $clientConfig = [
                'merchant_id'   => $method->getSetting('merchant_id'),
                'access_key'    => $method->getSetting('access_key'),
                'secret_key'    => $method->getSetting('secret_key'),
                'client_id'     => $method->getSetting('client_id'),
                'region'        => \XLite\Module\Amazon\PayWithAmazon\View\FormField\Select\Region::getRegionByCurrency($method->getSetting('region')),
                'currency_code' => $method->getSetting('region'),
                'sandbox'       => $processor->isTestMode($method),
            ];

            static::includeClient();

            return new \PayWithAmazon\Client($clientConfig);
        }, [__CLASS__, __FUNCTION__]);
    }

    public static function includeClient()
    {
        require_once LC_DIR_MODULES . 'Amazon' . LC_DS . 'PayWithAmazon' . LC_DS . 'lib' . LC_DS . 'PayWithAmazon' . LC_DS . 'Client.php';
    }

    public static function includeIPNHandler()
    {
        require_once LC_DIR_MODULES . 'Amazon' . LC_DS . 'PayWithAmazon' . LC_DS . 'lib' . LC_DS . 'PayWithAmazon' . LC_DS . 'IpnHandler.php';
    }

}
