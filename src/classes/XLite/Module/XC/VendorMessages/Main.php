<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\VendorMessages;

/**
 * Vendor messages module main class
 */
abstract class Main extends \XLite\Module\AModule
{
    /**
     * @inherited
     */
    public static function getAuthorName()
    {
        return 'X-Cart team';
    }

    /**
     * @inherited
     */
    public static function getMajorVersion()
    {
        return '5.3';
    }

    /**
     * @inherited
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
     * Get minor core version which is required for the module activation
     *
     * @return string
     */
    public static function getMinorRequiredCoreVersion()
    {
        return '2';
    }

    /**
     * @inherited
     */
    public static function getModuleName()
    {
        return 'Order messages';
    }

    /**
     * @inherited
     */
    public static function getDescription()
    {
        return 'The module enhances communication system in your store providing an opportunity for customers, '
            . 'administrator and vendors to start conversation or dispute about an order right on the Order details '
            . 'page.';
    }

    /**
     * @inherited
     */
    public static function showSettingsForm()
    {
        return true;
    }

    /**
     * Check - vendor messaging allowed or not
     *
     * @return boolean
     */
    public static function isVendorAllowed()
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Module')->isModuleEnabled('XC\MultiVendor')
            && (!\XLite\Module\XC\MultiVendor\Main::isWarehouseMode() || \XLite\Core\Config::getInstance()->XC->VendorMessages->allow_vendor_communication);
    }

    /**
     * Returns warehouse mode status
     *
     * @return boolean
     */
    public static function isWarehouse()
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Module')->isModuleEnabled('XC\MultiVendor')
            && \XLite\Module\XC\MultiVendor\Main::isWarehouseMode();
    }

    /**
     * Allow disputes or not
     *
     * @return boolean
     */
    public static function isAllowDisputes()
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Module')->isModuleEnabled('XC\MultiVendor')
            && (!static::isWarehouse() || static::isVendorAllowed());
    }

}
