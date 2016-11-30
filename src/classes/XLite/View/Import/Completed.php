<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Import;

/**
 * Completed section
 */
class Completed extends \XLite\View\AView
{
    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'import/completed.twig';
    }

    /**
     * Return massages
     *
     * @return array
     */
    protected function getMessages()
    {
        $result = array();

        if ($this->getImporter()) {
            foreach ($this->getImporter()->getSteps() as $step) {
                $result = array_merge($result, $step->getMessages());
            }
        }

        return $result;
    }

    /**
     * Get error messages
     *
     * @return array
     */
    protected function getErrorMessages()
    {
        $result = array();

        if (!\XLite\Core\Database::getRepo('XLite\Model\TmpVar')->getVar('importCancelFlag')) {
            if (!$this->hasCorrectFileNameFormat()) {
                // There are no valid CSV files found
                $result[] = array(
                    'text'    => static::t('CSV file has the wrong filename format.'),
                    'comment' => static::t('Possible import file names are:', array('files' => $this->getFileFormatsList())),
                );

            } else {
                // There are no data found in uploaded CSV files
                $result[] = array(
                    'text'    => static::t('X-Cart could not find data in your file.'),
                    'comment' => static::t(
                        'Possible reasons of data not found in import file',
                        array(
                            'separator' => \XLite\Core\Config::getInstance()->Units->csv_delim,
                            'encoding'  => \XLite\Core\Config::getInstance()->Units->export_import_charset,
                            'configURL' => $this->buildURL('units_formats'),
                            'kbURL'     => 'http://kb.x-cart.com/en/import-export/index.html',
                        )
                    ),
                );
            }
        }

        return $result;
    }

    /**
     * Get list of allowed import file formats
     *
     * @return string
     */
    protected function getFileFormatsList()
    {
        $result = array();

        $processors = $this->getImporter()
            ? $this->getImporter()->getProcessors()
            : \XLite\Logic\Import\Importer::getInstance()->getProcessors();

        foreach ($processors as $p) {
            $result[] = $p->getFileNameFormat();
        }

        $result = array_unique($result);

        return implode(', ', $result);
    }

    /**
     * Return true if CSV files have been processed by one of processors
     *
     * @return boolean
     */
    protected function hasCorrectFileNameFormat()
    {
        $result = false;

        $processors = $this->getImporter()
            ? $this->getImporter()->getProcessors()
            : \XLite\Logic\Import\Importer::getInstance()->getProcessors();

        foreach ($processors as $p) {
            if ($p->getFiles()) {
                $result = true;
                break;
            }
        }

        return $result;
    }

    /**
     * Get import event name
     *
     * @return string
     */
    protected function getEventName()
    {
        return \XLite\Logic\Import\Importer::getEventName();
    }
}
