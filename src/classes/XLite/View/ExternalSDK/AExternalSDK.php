<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\ExternalSDK;

/**
 * Abstract external SDK loader
 */
abstract class AExternalSDK extends \XLite\View\AView
{
    /**
     * Loaded state
     * 
     * @var boolean
     */
    protected static $loaded = array();

    /**
     * Check - loaded SDK or not
     * 
     * @return boolean
     */
    public static function isLoaded()
    {
        $class = get_called_class();

        return isset(static::$loaded[$class]) ? static::$loaded[$class] : false;
    }

    /**
     * Attempts to display widget using its template
     *
     * @param string $template Template file name OPTIONAL
     *
     * @return void
     */
    public function display($template = null)
    {
        parent::display($template);

        static::$loaded[get_called_class()] = true;
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && !static::isLoaded();
    }
}

