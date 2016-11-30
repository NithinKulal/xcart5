<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

/**
 * Controller for Database restore page
 * :TODO: must be completly refactored
 */
class DbRestore extends \XLite\Controller\Admin\Base\BackupRestore
{
    /**
     * Check if file is valid image
     *
     * @param string $path Temporary uploaded file path
     * @param string $name Real file name
     *
     * @return boolean
     */
    protected function isDumpFile($path, $name)
    {
        return \Includes\Utils\FileManager::isSQLExtension($name);
    }

    /**
     * doActionRestoreFromUploadedFile
     *
     * @return void
     * @throws
     */
    protected function doActionRestoreFromUploadedFile()
    {
        // Check uploaded file with SQL data
        if (isset($_FILES['userfile'])
            && !empty($_FILES['userfile']['tmp_name'])
            && $this->isDumpFile($_FILES['userfile']['tmp_name'], $_FILES['userfile']['name'])
        ) {

            $sqlFile = LC_DIR_TMP . sprintf('sqldump.uploaded.%d.sql', \XLite\Core\Converter::time());

            $tmpFile = $_FILES['userfile']['tmp_name'];

            if (!move_uploaded_file($tmpFile, $sqlFile)) {
                throw new \Exception(static::t('Error of uploading file.'));
            }

            $this->restoreDatabase($sqlFile);

            // Remove source SQL-file if it was uploaded
            unlink($sqlFile);

            $this->redirect();
            // Do not update session, etc.
            exit (0);
        } else {
            \XLite\Core\TopMessage::addError(
                'The "{{file}}" file is not allowed and was not uploaded. Allowed extensions are: {{extensions}}',
                array(
                    'file'          => $_FILES['userfile']['name'],
                    'extensions'    => 'sql',
                )
            );
        }
    }

    /**
     * doActionRestoreFromLocalFile
     *
     * @return void
     */
    protected function doActionRestoreFromLocalFile()
    {
        if (file_exists($this->sqldumpFile)) {
            $this->restoreDatabase($this->sqldumpFile);
            
            $this->redirect();
            
            // Do not update session, etc.
            exit (0);
        }
    }

    /**
     * Common restore database method used by actions
     *
     * @param mixed $sqlFile File with SQL data for loading into database
     *
     * @return boolean
     */
    protected function restoreDatabase($sqlFile)
    {
        $result = false;

        // File to create temporary backup to be able rollback database
        $backupSQLFile = LC_DIR_BACKUP . sprintf('sqldump.backup.%d.sql', \XLite\Core\Converter::time());

        // Make the process of restoring database verbose
        $verbose = true;

        // Start

        $this->startDump();

        // Making the temporary backup file
        \Includes\Utils\Operator::flush(static::t('Making backup of the current database state ... '), true);

        $result = \XLite\Core\Database::getInstance()->exportSQLToFile($backupSQLFile, $verbose);

        \Includes\Utils\Operator::flush(static::t('done') . LC_EOL . LC_EOL, true);

        // Loading specified SQL-file to the database
        \Includes\Utils\Operator::flush(static::t('Loading the database from file .'));

        $result = \Includes\Utils\Database::uploadSQLFromFile($sqlFile, $verbose);
        $restore = false;

        if ($result) {
            // If file has been loaded into database successfully
            $message = static::t('Database restored successfully!');

            // Prepare the cache rebuilding
            \XLite::setCleanUpCacheFlag(true);

        } else {
            // If an error occurred while loading file into database
            $message = static::t('The database has not been restored because of the errors');

            $restore = true;
        }

        // Display the result message
        \Includes\Utils\Operator::flush(
            ' ' . static::t('done') . LC_EOL
            . LC_EOL
            . $message . LC_EOL
        );

        if ($restore) {
            // Restore database from temporary backup
            \Includes\Utils\Operator::flush(LC_EOL . static::t('Restoring database from the backup .'));

            \Includes\Utils\Database::uploadSQLFromFile($backupSQLFile, $verbose);
            \Includes\Utils\Operator::flush(' ' . static::t('done') . LC_EOL . LC_EOL);
        }

        // Display Javascript to cancel scrolling page to bottom
        func_refresh_end();

        // Display the bottom HTML part
        $this->displayPageFooter();

        // Remove temporary backup file
        unlink($backupSQLFile);
        
        return $result;
    }

    /**
     * Get redirect mode - force redirect or not
     *
     * @return boolean
     */
    protected function getRedirectMode()
    {
        return true;
    }    

    /**
     * Returns helpful KB link
     * @return string
     */
    public function getKbUrl()
    {
        return 'http://kb.x-cart.com/en/general_setup/moving_x-cart_to_another_location.html#transfering-database';
    }
    
    /**
     * getPageReturnURL
     *
     * @return array
     */
    protected function getPageReturnURL()
    {
        $url = array();

        switch (\XLite\Core\Request::getInstance()->action) {

            case 'restore_from_uploaded_file':
            case 'restore_from_local_file':
                $url[] = '<a href="' . $this->buildURL('db_restore') . '">' . static::t('Return to admin interface') . '</a>';
                break;

            default:
                $url = parent::getPageReturnURL();
        }

        return $url;
    }
}
