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
class ModuleWarnings extends \XLite\View\ModulesManager\AModulesManager
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
     * Return title
     *
     * @return string
     */
    protected function getHead()
    {
        return 'Installation warnings';
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
     * Return templates directory name
     *
     * @return string
     */
    protected function getDir()
    {
        return parent::getDir() . '/warnings';
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible() && 'warnings' === \XLite\Core\Request::getInstance()->action;
    }
}
