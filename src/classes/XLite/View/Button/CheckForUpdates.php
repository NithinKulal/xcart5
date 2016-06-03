<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Button;

/**
 * Progress state button
 */
class CheckForUpdates extends \XLite\View\Button\ProgressState
{
    /**
     * Get a list of JavaScript files required to display the widget properly
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'button/js/check-for-updates.js';

        return $list;
    }

    /**
     * Get class
     *
     * @return string
     */
    protected function getClass()
    {
        return parent::getClass() . ' check-for-updates';
    }
}
