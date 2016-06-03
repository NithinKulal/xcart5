<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

/**
 * Import language page controller
 */
class LanguageImport extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Return page title
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Import language');
    }

    /**
     * Action 'import'
     *
     * @return void
     */
    protected function doActionImport()
    {
        $fileName = \XLite\Core\Session::getInstance()->language_import_file;

        if (\Includes\Utils\FileManager::isExists($fileName)) {
            $result = \XLite\Core\Database::getRepo('XLite\Model\Language')->parseImportFile($fileName, true);
            \XLite\Core\Session::getInstance()->language_import_result = $result;

        } else {
            \XLite\Core\Session::getInstance()->language_import_file = null;
            \XLite\Core\TopMessage::addError('File not found');
        }

        $this->setReturnURL($this->buildURL('languages'));
    }

    /**
     * Action 'Cancel import'
     *
     * @return void
     */
    protected function doActionCancelImport()
    {
        \XLite\Core\Session::getInstance()->language_import_file = null;
        \XLite\Core\Session::getInstance()->language_import_result = null;

        $this->setReturnURL($this->buildURL('languages'));
    }
}
