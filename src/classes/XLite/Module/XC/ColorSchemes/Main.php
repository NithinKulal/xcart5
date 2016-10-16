<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ColorSchemes;

/**
 * Module description
 *
 * @package XLite
 */
abstract class Main extends \XLite\Module\AModuleSkin
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
        return 'Color Schemes';
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
        return 'This module adds three new color schemes to the base X-Cart design theme.';
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
     * The following pathes are defined as substitutional skins:
     *
     * admin interface:     skins/custom_skin/admin/
     * customer interface:  skins/custom_skin/customer/
     * mail interface:      skins/custom_skin/mail/
     *
     * @return array
     */
    public static function getSkins()
    {
        return array(
            \XLite::CUSTOMER_INTERFACE  => array('XC_ColorSchemes/customer'),
        );
    }

    /**
     * Returns available layout colors
     *
     * @return array
     */
    public static function getLayoutColors()
    {
        return array(
            'Fashion' => \XLite\Core\Translation::lbl('Fashion'),
            'Noblesse' => \XLite\Core\Translation::lbl('Noblesse'),
            'Digital' => \XLite\Core\Translation::lbl('Digital'),
        );
    }

    /**
     * Defines the skin name
     * Currently it is defined from the configuration
     *
     * @return string
     */
    public static function getSkinName()
    {
        return \XLite\Core\Layout::getInstance()->getLayoutColor();
    }

    /**
     * Construct the CSS file name of the selected color scheme
     *
     * @return string
     */
    public static function getColorSchemeCSS()
    {
        return 'modules/XC/ColorSchemes/' . static::getSkinName() . '/style.css';
    }

    /**
     * Construct the Less file name of the selected color scheme
     *
     * @return string
     */
    public static function getColorSchemeLess()
    {
        return 'modules/XC/ColorSchemes/' . static::getSkinName() . '/style.less';
    }

    /**
     * Defines if the current skin is the default one
     *
     * @return boolean
     */
    public static function isDefaultColorScheme()
    {
        return !\XLite\Core\Layout::getInstance()->getLayoutColor();
    }
}
