<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Upgrade\Step\ReadyToInstall;

/**
 * DisabledModulesHooks
 */
class DisabledModulesHooks extends \XLite\View\Upgrade\Step\ReadyToInstall\AReadyToInstall
{
    /**
     * Get directory where template is located (body.twig)
     *
     * @return string
     */
    protected function getDir()
    {
        return parent::getDir() . '/disabled_modules_hooks';
    }

    /**
     * Return internal list name
     *
     * @return string
     */
    protected function getListName()
    {
        return parent::getListName() . '.disabled_modules_hooks';
    }

    /**
     * Check widget visibility
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible() && (bool) array_filter($this->getDisabledModulesHooks());
    }

    /**
     * Return list of files
     *
     * @return array
     */
    protected function getDisabledModulesHooks()
    {
        return \XLite\Upgrade\Cell::getInstance()->getDisabledModulesHooks();
    }
}
