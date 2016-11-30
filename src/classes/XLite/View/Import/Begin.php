<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Import;

/**
 * Begin section
 */
class Begin extends \XLite\View\AView
{
    const MODE_UPDATE_AND_CREATE = 'UC';
    const MODE_UPDATE_ONLY = 'U';
    const MODE_CREATE_ONLY = 'C';

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'import/begin.twig';
    }

    /**
     * Defines the message for uploading files
     *
     * @return string
     */
    protected function getUploadFileMessage()
    {
        return static::t(
            'CSV or ZIP files, total max size: {{size}}',
            array('size' => ini_get('upload_max_filesize'))
        );
    }

    /**
     * Return samples URL
     *
     * @return string
     */
    protected function getSamplesURL()
    {
        return 'http://kb.x-cart.com/en/import-export/index.html';
    }

    /**
     * Return samples URL text
     *
     * @return string
     */
    protected function getSamplesURLText()
    {
        return static::t('Import/Export guide');
    }

    /**
     * Check - charset enabledor not
     *
     * @return boolean
     */
    protected function isCharsetEnabled()
    {
        return \XLite\Core\Iconv::getInstance()->isValid();
    }

    /**
     * Return comment text for 'updateOnly' checkbox tooltip
     *
     * @return string
     */
    protected function getImportModeComment()
    {
        $result = '';

        $importer = $this->getImporter() ?: null;

        if (!$importer) {
            $importer = new \XLite\Logic\Import\Importer(array());
        }

        $keys = $importer->getAvailableEntityKeys();

        if ($keys) {
            $rows = array();
            foreach ($keys as $key => $list) {
                $rows[] = '<li>' . $key . ': <em>' . implode(', ', $list) . '</em></li>';
            }
            $result = static::t('Import mode comment', array('keys' => implode('', $rows)));
        }

        return $result;
    }

    /**
     * Get options for selector 'Import mode'
     *
     * @return array
     */
    protected function getImportModeOptions()
    {
        return array(
            static::MODE_UPDATE_AND_CREATE => static::t('Create new items and update existing items'),
            static::MODE_UPDATE_ONLY => static::t('Update existing items, but skip new items'),
        );
    }
}
