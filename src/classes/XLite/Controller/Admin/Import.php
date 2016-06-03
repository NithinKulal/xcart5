<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

/**
 * Import controller
 */
class Import extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Define the actions with no secure token
     *
     * @return array
     */
    public static function defineFreeFormIdActions()
    {
        $list = parent::defineFreeFormIdActions();
        $list[] = 'cancel';
        $list[] = 'proceed';
        $list[] = 'getErrorsFile';

        return $list;
    }


    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Import by CSV');
    }

    /**
     * Check ACL permissions
     *
     * @return boolean
     */
    public function checkACL()
    {
        return parent::checkACL() || \XLite\Core\Auth::getInstance()->isPermissionAllowed('manage import');
    }

    /**
     * Get importer
     *
     * @return \XLite\Logic\Import\Importer
     */
    public function getImporter()
    {
        if (!isset($this->importer)) {
            $state = \XLite\Core\Database::getRepo('XLite\Model\TmpVar')->getEventState($this->getEventName());
            $this->importer = ($state && isset($state['options']))
                ? new \XLite\Logic\Import\Importer($state['options'])
                : false;
        }

        return $this->importer;
    }

    /**
     * Get import target
     *
     * @return string
     */
    public function getImportTarget()
    {
        return $this->getImporter() ? $this->getImporter()->getOptions()->target : 'import';
    }

    /**
     * Import action
     *
     * @return void
     */
    protected function doActionImport()
    {
        foreach (\XLite\Logic\Import\Importer::getImportOptionsList() as $key) {
            \XLite\Core\Database::getRepo('XLite\Model\Config')->createOption(
                array(
                    'category' => 'Import',
                    'name'     => $key,
                    'value'    => isset(\XLite\Core\Request::getInstance()->options[$key])
                        ? \XLite\Core\Request::getInstance()->options[$key]
                        : false,
                )
            );
        }
        \XLite\Core\Config::updateInstance();

        $filesToImport = $this->getFilesToImport();

        if ($filesToImport) {
            \XLite\Logic\Import\Importer::run($this->getImportOptions(array('files' => $filesToImport)));
        }
    }

    /**
     * Get array of import options
     *
     * @param array $options Array of additional options OPTIONAL
     *
     * @return array
     */
    protected function getImportOptions($options = array())
    {
        return \XLite\Logic\Import\Importer::assembleImportOptions()
            + $options;
    }

    /**
     * Get list of files to import
     *
     * @return array
     */
    protected function getFilesToImport()
    {
        $dirTo = LC_DIR_VAR . \XLite\Logic\Import\Importer::getImportDir();

        if (!\Includes\Utils\FileManager::isExists($dirTo)) {
            \Includes\Utils\FileManager::mkdirRecursive($dirTo);
        }

        $filesToImport = array();
        if (
            $_FILES
            && isset($_FILES['files'])
            && $_FILES['files']['name']
            && $_FILES['files']['name'][0]
            && \Includes\Utils\FileManager::isDirWriteable($dirTo)
        ) {
            $list = glob($dirTo . LC_DS . '*');
            if ($list) {
                foreach ($list as $path) {
                    if (
                        is_file($path)
                        && \Includes\Utils\FileManager::isCSV($path)
                    ) {
                        \Includes\Utils\FileManager::deleteFile($path);

                    }
                }
            }

            $files = $_FILES['files'];
            foreach ($files['name'] as $key => $name) {
                $path = null;
                if (
                    $name
                    && UPLOAD_ERR_OK === $files['error'][$key]
                ) {
                    $path = \Includes\Utils\FileManager::getUniquePath($dirTo, $name ?: $files['name'][$key]);

                    if (move_uploaded_file($files['tmp_name'][$key], $path)) {
                        if (
                            \XLite\Core\Archive::getInstance()->isArchive($path)
                            || 'csv' == substr(strrchr($path, '.'), 1)
                        ) {
                            $filesToImport[] = $path;

                        } else {
                            \XLite\Core\TopMessage::addError(
                                'The "{{file}}" is not CSV or archive',
                                array('file' => $name)
                            );
                            \Includes\Utils\FileManager::deleteFile($path);
                        }

                    } else {
                        $path = null;
                    }
                }

                if (!$path) {
                    \XLite\Core\TopMessage::addError(
                        'The "{{file}}" file was not uploaded',
                        array('file' => $name)
                    );
                }
            }
        }

        return $filesToImport;
    }

    /**
     * Preprocessor for no-action run
     *
     * @return void
     */
    protected function doNoAction()
    {
        if (
            $this->getImporter()
            && $this->getImporter()->isNextStepAllowed()
        ) {
            $this->getImporter()->switchToNextStep();
        }
    }

    /**
     * Preprocessor for proceed action
     *
     * @return void
     */
    protected function doActionProceed()
    {
        if ($this->getImporter()) {
            if (!empty($this->getImporter()->getOptions()->commonData['finalize'])) {
                $this->getImporter()->getOptions()->commonData['finalize'] = null;
            }

            $this->getImporter()->getOptions()->warningsAccepted = true;

            if ($this->getImporter()->isNextStepAllowed()) {
                $this->getImporter()->switchToNextStep(array('warningsAccepted' => true));
                \XLite\Core\Database::getRepo('XLite\Model\ImportLog')->deleteByType(\XLite\Model\ImportLog::TYPE_WARNING);
            }
        }
    }

    /**
     * Cancel
     *
     * @return void
     */
    protected function doActionCancel()
    {
        if ($this->getImporter()->getOptions()->clearImportDir) {
            $this->getImporter()->deleteAllFiles();
        }

        if (\XLite\Logic\Import\Importer::hasErrors() || \XLite\Logic\Import\Importer::hasWarnings()) {
            \XLite\Logic\Import\Importer::userBreak();

        } else {
            \XLite\Logic\Import\Importer::cancel();
            \XLite\Core\TopMessage::addWarning('Import has been cancelled.');
        }
    }

    /**
     * Reset
     *
     * @return void
     */
    protected function doActionReset()
    {
        if ($this->getImporter()->getOptions()->clearImportDir) {
            $this->getImporter()->deleteAllFiles();
        }

        \XLite\Logic\Import\Importer::cancel();

        $this->setReturnURL($this->buildURL($this->getImportTarget()));
    }

    /**
     * Reset
     *
     * @return void
     */
    protected function doActionGetErrorsFile()
    {
        header('Content-Type: application/force-download');
        header('Content-Disposition: attachment; filename="errors.txt"');

        $template  = 'import/errors-file.twig';
        $viewer = new \XLite\View\Import\ErrorsFile();
        $content = $viewer->getContent();
        print $content;
        $this->setSuppressOutput(true);
        $this->set('silent', true);

        exit(0);
    }

    /**
     * Return current step
     *
     * @return integer
     */
    public function getCurrentStep()
    {
        $state = \XLite\Core\Database::getRepo('XLite\Model\TmpVar')->getEventState($this->getEventName());

        return ($state && isset($state['options']) && isset($state['options']['step']))
            ? $state['options']['step']
            : 0;
    }

    /**
     * Get event name
     *
     * @return string
     */
    protected function getEventName()
    {
        return \XLite\Logic\Import\Importer::getEventName();
    }
}
