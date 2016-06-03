<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\LanguagesModify;

/**
 * Language import process page
 *
 * @ListChild (list="admin.center", zone="admin")
 */
class LanguageImport extends \XLite\View\SimpleDialog
{
    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();
        $list[] = 'language_import';

        return $list;
    }


    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getBody()
    {
        return 'languages/import/body.twig';
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible() && (boolean)$this->getImportFilePath();
    }

    /**
     * Return title
     *
     * @return string
     */
    protected function getHead()
    {
        return 'Import language';
    }

    /**
     * Get import file path
     *
     * @return string
     */
    protected function getImportFilePath()
    {
        return \XLite\Core\Session::getInstance()->language_import_file;
    }

    /**
     * Return status of import file checking (true on success, false on failure)
     *
     * @return boolean
     */
    protected function isSuccess()
    {
        $importData = $this->parseImportFile();

        return 0 < $importData['codes'];
    }

    /**
     * Get error message
     *
     * @return string
     */
    protected function getMessage()
    {
        return null;
    }

    /**
     * Get array with summary information about import file data
     *
     * @return array
     */
    protected function getImportFileData()
    {
        $result = array();

        $importData = $this->parseImportFile();

        foreach ($importData['codes'] as $code => $lngData) {
            $result['codes'][] = array(
                'code'         => strtoupper($code),
                'language'     => $lngData['language'],
                'labels_count' => $lngData['count'],
            );
        }

        $result['ignored'] = $importData['ignored'];
        $result['elapsed'] = $importData['elapsed'];

        return $result;
    }

    /**
     * Get result of parsing import file
     *
     * @return array
     */
    protected function parseImportFile()
    {
        return \XLite\Core\Session::getInstance()->language_import_result
            ?: \XLite\Core\Database::getRepo('XLite\Model\Language')->parseImportFile($this->getImportFilePath());
    }

    /**
     * Return true if import has been finished and reset import state flags
     *
     * @return boolean
     */
    protected function isImportFinished()
    {
        return (boolean)\XLite\Core\Session::getInstance()->language_import_result;
    }
}
