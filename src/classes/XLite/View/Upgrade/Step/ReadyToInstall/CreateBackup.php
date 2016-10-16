<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Upgrade\Step\ReadyToInstall;

/**
 * CreateBackup
 */
class CreateBackup extends \XLite\View\Upgrade\Step\ReadyToInstall\AReadyToInstall
{
    const BACKUP_MASTER_NAME = 'QSL\Backup';

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = self::getDir() . '/js/script.js';

        return $list;
    }

    /**
     * Get directory where template is located (body.twig)
     *
     * @return string
     */
    protected function getDir()
    {
        return parent::getDir() . '/create_backup';
    }

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = self::getDir() . '/css/style.css';

        return $list;
    }

    /**
     * Return internal list name
     *
     * @return string
     */
    protected function getListName()
    {
        return parent::getListName() . '.create_backup';
    }

    /**
     * Check if Backup Master enabled
     *
     * @return bool
     */
    protected function isBackupMasterEnabled()
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Module')->isModuleEnabled(static::BACKUP_MASTER_NAME);
    }

    /**
     * Return link to backup module
     *
     * @return string
     */
    protected function getBackupModuleLink()
    {
        if ($this->isBackupMasterEnabled()) {
            return $this->buildURL('backup');
        }

        list($author, $module) = explode('\\', static::BACKUP_MASTER_NAME);

        return \XLite\Core\Database::getRepo('XLite\Model\Module')->getMarketplaceUrlByName($author, $module);
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible();
    }
}
