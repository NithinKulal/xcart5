<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CrispWhiteSkin\View;

/**
 * Product comparison widget
 *
 * @ListChild (list="layout.header.right", weight="30")
 */
class HeaderSettings extends \XLite\View\AView
{
    /**
     * Return list of JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'js/header_settings.js';

        return $list;
    }

    /**
     * Return list of CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = array(
            'file'  => 'css/header_settings.less',
            'media' => 'screen',
            'merge' => 'bootstrap/css/bootstrap.less',
        );

        return $list;
    }

    /**
     * Return default template path
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'layout/header/header.right.settings.twig';
    }

    /**
     * Check if recently updated
     *
     * @return bool
     */
    protected function isRecentlyUpdated()
    {
        return false;
    }

    /**
     * Return classes list
     *
     * @return array
     */
    protected function getHeaderSettingsClassesList()
    {
        $list = [];

        if ($this->isRecentlyUpdated()) {
            $list[] = 'recently-updated';
        }

        return $list;
    }

    /**
     * Return classes
     *
     * @return string
     */
    protected function getHeaderSettingsClasses()
    {
        return implode(' ', $this->getHeaderSettingsClassesList());
    }
}
