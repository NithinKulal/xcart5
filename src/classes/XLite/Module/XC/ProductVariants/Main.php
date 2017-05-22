<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductVariants;

/**
 * Product comparison module main class
 *
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
     * Module version
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
        return '7';
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
     * Module name
     *
     * @return string
     */
    public static function getModuleName()
    {
        return 'Product Variants';
    }

    /**
     * Module description
     *
     * @return string
     */
    public static function getDescription()
    {
        return 'Allows to manage products variants.';
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
     * Check if product price in list should be displayed as range
     *
     * @return bool
     */
    public static function isDisplayPriceAsRange()
    {
        return \XLite\Core\Config::getInstance()->XC->ProductVariants->price_in_list == \XLite\Module\XC\ProductVariants\View\FormField\Select\PriceInList::DISPLAY_RANGE;
    }

    /**
     * Method to call just after the module is installed
     *
     * @return void
     */
    public static function callInstallEvent()
    {
        $qb = \XLite\Core\Database::getEM()->createQueryBuilder();
        $qb->update('XLite\Model\QuickData', 'qd')
            ->set('qd.minPrice', 'qd.price')
            ->set('qd.maxPrice', 'qd.price')
            ->getQuery()
            ->execute();
    }
}
