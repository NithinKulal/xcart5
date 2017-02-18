<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Upgrade;

/**
 * InstallUpdates
 *
 * @ListChild (list="admin.center", zone="admin")
 */
class InstallUpdates extends \XLite\View\Upgrade\AUpgrade
{
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

        $list[] = $this->getDir() . '/controller.js';

        return $list;
    }

    /**
     * Get directory where template is located (body.twig)
     *
     * @return string
     */
    protected function getDir()
    {
        return parent::getDir() . '/install_updates';
    }

    /**
     * Return internal list name
     *
     * @return string
     */
    protected function getListName()
    {
        return parent::getListName() . '.install_updates';
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible() && $this->getUpgradeEntries() && $this->isUpdate();
    }

    /**
     * Return true if advanced mode is enabled
     *
     * @return boolean
     */
    protected function isAdvancedMode()
    {
        return (bool) \XLite\Core\Request::getInstance()->advanced;
    }

    /**
     * Return true if entry is selectable in the entries list (in advanced mode)
     *
     * @return boolean
     */
    protected function isEntrySelectable(\XLite\Upgrade\Entry\AEntry $entry)
    {
        return $this->isAdvancedMode()
            && (
                !$this->isModule($entry)
                || !(
                    $entry->isSystem()
                    && \XLite\Upgrade\Cell::getInstance()->hasCoreUpdate()
                )
            );
    }

    /**
     * Get URL for 'Advanced mode' button
     *
     * @return string
     */
    protected function getAdvancedModeURL()
    {
        $params = array(
            'mode'     => 'install_updates',
            'advanced' => 1,
        );

        return $this->buildURL('upgrade', '', $params);
    }

    /**
     * Get label for 'Advanced mode' button
     *
     * @return string
     */
    protected function getAdvancedModeButtonLabel()
    {
        return 'Advanced mode';
    }

    /**
     * Return true if 'Advanced mode' button should be displayed
     *
     * @return boolean
     */
    protected function isAdvancedModeButtonVisible()
    {
        return !$this->isAdvancedMode() && 1 < count($this->getUpgradeEntries());
    }

    /**
     * @param \XLite\Upgrade\Entry\AEntry $entry
     *
     * @return boolean
     */
    public function isAvailableForUpgradeWithoutCore(\XLite\Upgrade\Entry\AEntry $entry)
    {
        return $entry instanceof \XLite\Upgrade\Entry\Module\Marketplace
            ? $entry->isAvailableForUpgradeWithoutCore()
            : true;
    }

    /**
     * Get module ID
     *
     * @return string
     */
    protected function getModuleID(\XLite\Upgrade\Entry\AEntry $entry)
    {
        return $entry->getModuleEntryID();
    }
}
