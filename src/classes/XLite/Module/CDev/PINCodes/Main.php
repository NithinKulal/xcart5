<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\PINCodes;

/**
 * Main
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
     * Module name
     *
     * @return string
     */
    public static function getModuleName()
    {
        return 'PIN Codes';
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
        return '1';
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
     * Module description
     *
     * @return string
     */
    public static function getDescription()
    {
        return 'Allows to sell PIN codes attached to products.';
    }

    /**
     * Removes some inventory tracking templates to extend them with PIN Codes specific features
     *
     * @return array
     */
    protected static function moveTemplatesInLists()
    {
        return array(
            'product/inventory/inv_track_amount.twig' => array(
                static::TO_DELETE => array(
                    array('product.inventory.parts', \XLite\Model\ViewList::INTERFACE_ADMIN),
                ),
            ),
            'product/inventory/inv_track_selector.twig' => array(
                static::TO_DELETE => array(
                    array('product.inventory.parts', \XLite\Model\ViewList::INTERFACE_ADMIN),
                ),
            )
        );
    }
}
