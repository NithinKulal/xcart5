<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\ModulesManager;

/**
 * Addons search and installation widget
 *
 * @ListChild (list="admin.center", zone="admin")
 */
class ModuleLicense extends \XLite\View\ModulesManager\AModulesManager
{
    /**
     * Module license widget target
     */
    const MODULE_LICENSE_TARGET = 'addon_install';

    /**
     * Return list of targets allowed for this widget
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $result = parent::getAllowedTargets();
        $result[] = self::MODULE_LICENSE_TARGET;

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
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = $this->getDir() . '/js/switch-button.js';

        return $list;
    }

    /**
     * Return title
     *
     * @return string
     */
    protected function getHead()
    {
        return 'Install addon';
    }

    /**
     * Return templates directory name
     *
     * @return string
     */
    protected function getDir()
    {
        return parent::getDir() . '/license';
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible() && 'view_license' === \XLite\Core\Request::getInstance()->action;
    }
}
