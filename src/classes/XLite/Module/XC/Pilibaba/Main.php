<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Pilibaba;

/**
 * Main module
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
     * Module name
     *
     * @return string
     */
    public static function getModuleName()
    {
        return 'Pilibaba Chinese Checkout';
    }

    /**
     * Module description
     *
     * @return string
     */
    public static function getDescription()
    {
        return 'Start accepting CNY payment and take orders from 1.3 Billion shoppers in China. Support all Chinese debit/credit cards and give merchants the flexibility to convert CNY payment into either USD,EUR or 12 supported currencies.';
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
        return '1';
    }

    /**
     * Get module type
     *
     * @return integer
     */
    public static function getModuleType()
    {
        return static::MODULE_TYPE_PAYMENT;
    }

    /**
     * Include libraries
     */
    public static function includeLibrary()
    {
        require_once LC_DIR_MODULES . 'XC' . LC_DS . 'Pilibaba' . LC_DS . 'lib' . LC_DS . 'pilipay' . LC_DS . 'autoload.php';
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
            '\XLite\Module\XC\Pilibaba\Model\Shipping\Processor\Pilibaba'
        );
        if (static::getPaymentMethod()->isEnabled()) {
            \XLite\Logic\AllInOneSolutionService::getInstance()->addSolution(
                new \XLite\Module\XC\Pilibaba\View\Button\PilibabaCheckout(),
                'pilibaba'
            );
        }
    }

    /**
     * Get payment method
     *
     * @return \XLite\Model\Payment\Method
     */
    public static function getPaymentMethod()
    {
        return \XLite\Core\Database::getRepo('\XLite\Model\Payment\Method')->findOneBy(
            array(
                'service_name' => 'Pilibaba'
            )
        );
    }

    /**
     * Defines the link for the payment settings form
     *
     * @return string
     */
    public static function getPaymentSettingsForm()
    {
        $method = static::getPaymentMethod();

        return $method && $method->getAdded()
            ? $method->getProcessor()->getConfigurationURL($method)
            : parent::getPaymentSettingsForm();
    }
}
