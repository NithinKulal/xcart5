<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Quantum;

/**
 * Quantum payment gateway integration (QGWdatabase Engine API)
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
        return 'QuantumGateway';
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
     * Module description
     *
     * @return string
     */
    public static function getDescription()
    {
        return 'Enables taking credit card payments for your online store via QuantumGateway (QGWdatabase Engine API).';
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
