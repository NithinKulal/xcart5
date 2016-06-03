<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\ModulesManager;

/**
 * Install updates or not
 *
 * @ListChild (list="admin.center", zone="admin")
 */
class InstallationType extends \XLite\View\ModulesManager\AModulesManager
{
    /**
     * Return list of targets allowed for this widget
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $result = parent::getAllowedTargets();
        $result[] = 'addon_install';

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
     * Return title
     *
     * @return string
     */
    protected function getHead()
    {
        return 'Updates are available';
    }

    /**
     * Return templates directory name
     *
     * @return string
     */
    protected function getDir()
    {
        return parent::getDir() . '/installation_type';
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible() && 'select_installation_type' === \XLite\Core\Request::getInstance()->action;
    }
}
