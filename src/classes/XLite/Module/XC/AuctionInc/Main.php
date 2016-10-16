<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\AuctionInc;

/**
 * AuctionInc module main class
 *
 */
abstract class Main extends \XLite\Module\AModule
{
    /**
     * Trial period duration (in seconds)
     * Default: 1 month (30 days)
     */
    const TRIAL_PERIOD_DURATION = 2592000;

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
        return 'AuctionInc ShippingCalc';
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
     * Get module build number (4th number in the version)
     *
     * @return string
     */
    public static function getBuildVersion()
    {
        return '2';
    }

    /**
     * Module description
     *
     * @return string
     */
    public static function getDescription()
    {
        return <<<TEXT
Provide your customers with accurate "real-time" comparative domestic or international shipping rates from
your choice of services from Fedex, UPS, USPS and DHL. No carrier accounts required. Full support for your
item dimensions and dimensional rates. Shipping origins from any country supported. A host of advanced
features include: shipping promotions, bundled handling fees, drop-shipping from multiple origins, and a
packaging engine that accurately predicts appropriate packaging for multiple items and quantities. Free
month-long trial, then subscription to AuctionInc required.
TEXT;
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
        return \XLite\Core\Converter::buildURL('auction_inc');
    }

    /**
     * Perform some actions at startup
     *
     * @return string
     */
    public static function init()
    {
        parent::init();

        \XLite\Model\Shipping::getInstance()->registerProcessor(
            '\XLite\Module\XC\AuctionInc\Model\Shipping\Processor\AuctionInc'
        );
    }

    /**
     * Method to call just after the module is installed
     *
     * @return void
     */
    public static function callInstallEvent()
    {
        static::generateHeaderReferenceCode();
        static::generateFirstUsageDate();
    }

    /**
     * The module is defined as the shipping module
     *
     * @return integer|null
     */
    public static function getModuleType()
    {
        return static::MODULE_TYPE_SHIPPING;
    }

    /**
     * Return true if module should work in strict mode
     * (strict mode enables the logging of errors like 'The module is not configured')
     *
     * @return boolean
     */
    public static function isStrictMode()
    {
        return false;
    }

    // {{{ HeaderReferenceCode

    /**
     * Generate first usage date
     *
     * @return string
     */
    public static function generateHeaderReferenceCode()
    {
        $code = 'XC5-' . md5(LC_START_TIME);
        \XLite\Core\Database::getRepo('XLite\Model\Config')->createOption(array(
            'category' => 'XC\AuctionInc',
            'name' => 'headerReferenceCode',
            'value' => $code,
        ));

        return $code;
    }

    /**
     * Returns HeaderReferenceCode
     *
     * @return string
     */
    public static function getHeaderReferenceCode()
    {
        return static::getConfiguration()->headerReferenceCode
            ?: static::generateHeaderReferenceCode();
    }

    // }}}

    // {{{ XS trial period

    /**
     * Generate first usage date
     *
     * @return void
     */
    public static function generateFirstUsageDate()
    {
        \XLite\Core\Database::getRepo('XLite\Model\Config')->createOption(array(
            'category' => 'XC\AuctionInc',
            'name' => 'firstUsageDate',
            'value' => LC_START_TIME,
        ));
    }

    /**
     * Check XS trial period
     *
     * @return boolean
     */
    public static function isXSTrialPeriodValid()
    {
        $firstUsageDate = static::getConfiguration()->firstUsageDate;
        $result = true;

        if ($firstUsageDate) {
            $result = LC_START_TIME < $firstUsageDate + static::TRIAL_PERIOD_DURATION;

        } else {
            static::generateFirstUsageDate();
        }

        return $result;
    }

    /**
     * Check if SS available
     *
     * @return boolean
     */
    public static function isSSAvailable()
    {
        return (bool) static::getConfiguration()->accountId;
    }

    /**
     * Check if XS available
     *
     * @return boolean
     */
    public static function isXSAvailable()
    {
        return !static::isSSAvailable()
            && static::isXSTrialPeriodValid();
    }

    /**
     * Returns shipping method activity status
     *
     * @return boolean
     */
    protected static function isMethodEnabled()
    {
        /** @var \XLite\Model\Repo\Shipping\Method $repo */
        $repo = \XLite\Core\Database::getRepo('XLite\Model\Shipping\Method');
        $onlineMethod = $repo->findOnlineCarrier('auctionInc');

        return $onlineMethod ? $onlineMethod->getEnabled() : true;
    }

    /**
     * Returns configuration
     *
     * @return \XLite\Core\ConfigCell
     */
    protected static function getConfiguration()
    {
        return \XLite\Core\Config::getInstance()->XC->AuctionInc;
    }

    // }}}
}
