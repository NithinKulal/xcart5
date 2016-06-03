<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

/**
 * Currency management page widget
 *
 */
class CurrencyViewInfo extends \XLite\View\Dialog
{
    /**
     * Return list of targets allowed for this widget
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $result = parent::getAllowedTargets();
        $result[] = 'currency';

        return $result;
    }

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = $this->getDir() . '/css/style.css';

        return $list;
    }

    /**
     * getJSFiles
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = $this->getDir() . '/js/script.js';

        return $list;
    }

    /**
     * Return templates directory name
     *
     * @return string
     */
    protected function getDir()
    {
        return 'currency_view_info';
    }

    /**
     * Return specific CSS class for dialog wrapper
     *
     * @return string
     */
    protected function getDialogCSSClass()
    {
        return parent::getDialogCSSClass() . ' currency-view-info-dialog';
    }
}
