<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Upgrade\Step\Prepare;

/**
 * APrepare
 */
abstract class APrepare extends \XLite\View\Upgrade\Step\AStep
{
    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = self::getDir() . '/css/style.css';

        // Must be called from this class
        if ($this->isUpgrade()) {
            $list[] = self::getDir() . '/css/upgrade.css';
        }

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
        $list[] = self::getDir() . '/js/controller.js';

        return $list;
    }

    /**
     * Get directory where template is located (body.twig)
     *
     * @return string
     */
    protected function getDir()
    {
        return parent::getDir() . '/prepare';
    }

    /**
     * Return internal list name
     *
     * @return string
     */
    protected function getListName()
    {
        return parent::getListName() . '.prepare';
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && $this->getUpgradeEntries()
            && !\XLite\Upgrade\Cell::getInstance()->isUnpacked()
            && !\XLite\Upgrade\Cell::getInstance()->isUpgraded();
    }

    /**
     * Returns installed module url
     *
     * @param \XLite\Model\Module $module Module
     *
     * @return string
     */
    protected function getInstalledModuleURL($module)
    {
        return $module->getInstalledURL();
    }

    /**
     * Return true if cell is an upgrade cell (changes major version)
     *
     * @return boolean
     */
    protected function isUpgrade()
    {
        return \XLite\Upgrade\Cell::getInstance()->isUpgrade();
    }
}
