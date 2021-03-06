<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\IdealPayments;

/**
 * IdealPayments module
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
        return 'iDEAL Payments';
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
     * Module description
     *
     * @return string
     */
    public static function getDescription()
    {
        return 'Enables taking credit card payments for your online store via iDEAL payment system.';
    }

    /**
     * Add record to the module log file
     *
     * @param string $message Text message OPTIONAL
     * @param mixed  $data    Data (can be any type) OPTIONAL
     *
     * @return void
     */
    public static function addLog($message = null, $data = null)
    {
        if ($message && $data) {
            $msg = array(
                'message' => $message,
                'data'    => $data,
            );

        } else {
            $msg = ($message ?: ($data ?: null));
        }

        if (!is_string($msg)) {
            $msg = var_export($msg, true);
        }

        \XLite\Logger::logCustom(
            self::getModuleName(),
            $msg
        );
    }

    /**
     * Get path of SDK classes file
     *
     * @return string
     */
    public static function getLibClassesFile()
    {
        return LC_DIR_MODULES . 'XC' . LC_DS . 'IdealPayments' . LC_DS . 'lib' . LC_DS . 'IdealProRequest.php';
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
}
