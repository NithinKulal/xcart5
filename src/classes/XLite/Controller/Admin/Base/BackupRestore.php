<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin\Base;

/**
 * Base controller for Backup/Restore section
 */
abstract class BackupRestore extends \XLite\Controller\Admin\AAdmin
{
    /**
     * sqldumpFile
     *
     * @var mixed
     */
    protected $sqldumpFile = null;


    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Tools');
    }

    /**
     * handleRequest
     *
     * @return void
     */
    public function handleRequest()
    {
        if (\XLite\Core\Request::getInstance()->isPost()) {
            set_time_limit(0);
        }
        $this->sqldumpFile = LC_DIR_BACKUP . 'sqldump.sql.php';

        parent::handleRequest();
    }

    /**
     * isFileExists
     *
     * @return boolean
     */
    public function isFileExists()
    {
        return \Includes\Utils\FileManager::isExists($this->sqldumpFile);
    }

    /**
     * isFileWritable
     *
     * @return boolean
     */
    public function isFileWritable()
    {
        return $this->isDirExists()
            && (
                !$this->isFileExists()
                || \Includes\Utils\FileManager::isFileWriteable($this->sqldumpFile)
            );
    }

    /**
     * isDirExists
     *
     * @return boolean
     */
    public function isDirExists()
    {
        if (!is_dir(LC_DIR_BACKUP)) {
            \Includes\Utils\FileManager::mkdirRecursive(LC_DIR_BACKUP);
        }
        return \Includes\Utils\FileManager::isDirWriteable(LC_DIR_BACKUP);
    }
}
