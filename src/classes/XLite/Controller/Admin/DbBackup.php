<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

/**
 * Controller for Database backup page
 * :TODO: must be completly refactored
 */
class DbBackup extends \XLite\Controller\Admin\Base\BackupRestore
{
    /**
     * doActionBackup
     *
     * @return void
     */
    protected function doActionBackup()
    {
        $destFile = LC_DIR_BACKUP . sprintf('sqldump.backup.%d.sql', \XLite\Core\Converter::time());
        $this->startDownload('db_backup.sql');
        // Make database backup and store it in $this->sqldumpFile file
        \XLite\Core\Database::getInstance()->exportSQLToFile($destFile, false);
        
        readfile($destFile);
        \Includes\Utils\FileManager::deleteFile($destFile);

        exit ();
    }

    /**
     * doActionBackupWriteToFile
     *
     * @return void
     */
    protected function doActionBackupWriteToFile()
    {
        $destFile = $this->sqldumpFile;
        $this->startDump();
        // Make database backup and store it in $this->sqldumpFile file
        \XLite\Core\Database::getInstance()->exportSQLToFile($destFile, true);
        \XLite\Core\TopMessage::addInfo('Database backup created successfully');

        $this->setReturnURL($this->buildURL('db_backup'));
        $this->doRedirect();
    }

    /**
     * doActionDelete
     *
     * @return void
     * @throws
     */
    protected function doActionDelete()
    {
        if (
            \Includes\Utils\FileManager::isExists($this->sqldumpFile)
            && !\Includes\Utils\FileManager::deleteFile($this->sqldumpFile)
        ) {
            \XLite\Core\TopMessage::addError(static::t('Unable to delete file') . ' ' . $this->sqldumpFile);
        } else {
            \XLite\Core\TopMessage::addInfo('SQL file was deleted successfully');
        }
        $this->doRedirect();
    }
}
