<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Input\Checkbox;

/**
 * Switch
 */
class SwitcherReadOnly extends \XLite\View\FormField\Input\Checkbox\Switcher
{
    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = $this->getDir() . '/input/checkbox/switcher_read_only.css';

        return $list;
    }

    /**
     * Get 'Disable' label
     *
     * @return string
     */
    protected function getEnabledLabel()
    {
        return 'Enabled in catalog';
    }

    /**
     * Get 'Enable' label
     *
     * @return string
     */
    protected function getDisabledLabel()
    {
        return 'Disabled in catalog';
    }

    /**
     * Define the specific CSS class for according the switcher type
     *
     * @return string
     */
    protected function getTypeSwitcherClass()
    {
        return 'switcher-read-only';
    }

    /**
     * Defines the specific switcher JS file
     *
     * @return array
     */
    protected function getWidgetJSFiles()
    {
        return array();
    }

    /**
     * Get default wrapper class
     *
     * @return string
     */
    protected function getDefaultWrapperClass()
    {
        return trim(parent::getDefaultWrapperClass() . ' switcher-read-only');
    }
}
