<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Input\Text;

/**
 * Color
 */
class Color extends \XLite\View\FormField\Input\Text
{
    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = 'form_field/input/text/color.js';

        return $list;
    }

    /**
     * Register files from common repository
     *
     * @return array
     */
    protected function getCommonFiles()
    {
        $list = parent::getCommonFiles();

        $list[static::RESOURCE_JS][]  = 'colorpicker/js/colorpicker.js';
        $list[static::RESOURCE_CSS][] = 'colorpicker/css/colorpicker.css';

        return $list;
    }

    /**
     * Assemble classes
     *
     * @param array $classes Classes
     *
     * @return array
     */
    protected function assembleClasses(array $classes)
    {
        $classes = parent::assembleClasses($classes);

        $classes[] = 'color';

        return $classes;
    }

    /**
     * Get default maximum size
     *
     * @return integer
     */
    protected function getDefaultMaxSize()
    {
        return 6;
    }
}
