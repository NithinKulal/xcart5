<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Input\Checkbox;

class FirstLast extends \XLite\View\FormField\Input\Checkbox\OnOff
{
    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = $this->getDir() . '/input/checkbox/first_last.css';

        return $list;
    }

    /**
     * Returns default param value
     *
     * @return string
     */
    protected function getDefaultOnLabel()
    {
        // First
        return 'checkbox.firstlast.on';
    }

    /**
     * Returns default param value
     *
     * @return string
     */
    protected function getDefaultOffLabel()
    {
        // Last
        return 'checkbox.firstlast.off';
    }

    /**
     * Returns default param value
     *
     * @return string
     */
    protected function getDefaultCssClass()
    {
        return 'first-last-switch';
    } 
}