<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Upgrade\Step\ReadyToInstall;

/**
 * PreUpgradeWarningModules
 */
class PreUpgradeWarningModules extends \XLite\View\Upgrade\Step\ReadyToInstall\AReadyToInstall
{
    /**
     * Get directory where template is located (body.twig)
     *
     * @return string
     */
    protected function getDir()
    {
        return parent::getDir() . '/pre_upgrade_warning_modules';
    }

    /**
     * Return internal list name
     *
     * @return string
     */
    protected function getListName()
    {
        return parent::getListName() . '.pre_upgrade_warning_modules';
    }

    /**
     * Check widget visibility
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible() && (bool) array_filter($this->getPreUpgradeWarningModules());
    }

    /**
     * Return list of files
     *
     * @return array
     */
    protected function getPreUpgradeWarningModules()
    {
        return \XLite\Upgrade\Cell::getInstance()->getPreUpgradeWarningModules();
    }
}
