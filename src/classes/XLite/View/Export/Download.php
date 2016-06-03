<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Export;

/**
 * Download files box
 */
class Download extends \XLite\View\AView
{

    /**
     * Widget parameters
     */
    const PARAM_COMPLETED_CONTEXT = 'completedContext';
    const PARAM_POPUP_CONTEXT = 'popupContext';

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'export/download.twig';
    }

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            static::PARAM_COMPLETED_CONTEXT => new \XLite\Model\WidgetParam\TypeBool('Complete section context', false),
            static::PARAM_POPUP_CONTEXT => new \XLite\Model\WidgetParam\TypeBool('Popup context', false),
        );
    }

    /**
     * Check - widget run into completed section context or not
     *
     * @return boolean
     */
    protected function isCompletedSection()
    {
        return $this->getParam(static::PARAM_COMPLETED_CONTEXT);
    }

    /**
     * Check - widget run into popup section context or not
     *
     * @return boolean
     */
    protected function isPopupContext()
    {
        return $this->getParam(static::PARAM_POPUP_CONTEXT);
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible() && $this->getDownloadFiles();
    }

    /**
     * Check if bracket is visible
     *
     * @return boolean
     */
    protected function isBracketVisible()
    {
        return 1 < count($this->getDownloadFiles());
    }

    /**
     * Get box title
     *
     * @return string
     */
    protected function getBoxTitle()
    {
        return $this->isCompletedSection()
            ? static::t('Download CSV files')
            : static::t('Exported in X', array('date' => $this->getLastExportDate()));
    }

    /**
     * Get last export date
     *
     * @return string
     */
    protected function getLastExportDate()
    {
        $list = $this->getDownloadFiles();
        $file = $list ? current($list) : null;

        return \XLite\Core\Converter::formatDate($file ? $file->getMTime() : \XLite\Core\Converter::time());

    }

    /**
     * Get download files
     *
     * @return array
     */
    protected function getDownloadFiles()
    {
        $result = array();

        if ($this->getGenerator()) {
            foreach ($this->getGenerator()->getDownloadableFiles() as $path) {
                if (preg_match('/\.csv$/Ss', $path)) {
                    $key = basename($path);
                    $result[$key] = new \SplFileInfo($path);
                }
            }
        }

        return $result;
    }

    /**
     * Get download large files
     *
     * @return array
     */
    protected function getDownloadLargeFiles()
    {
        $result = array();

        $len = strlen($this->getGenerator()->getOptions()->dir) + 1;
        foreach ($this->getGenerator()->getDownloadableFiles() as $path) {
            if (filesize($path) >= \XLite\Logic\Export\Generator::MAX_FILE_SIZE) {
                $key = substr($path, $len);
                $result[$key] = new \SplFileInfo($path);
            }
        }

        return $result;
    }

    /**
     * Get packed size
     *
     * @return integer
     */
    protected function getPackedSize()
    {
        $size = 0;

        foreach ($this->getGenerator()->getPackedFiles() as $path) {
            if (filesize($path) < \XLite\Logic\Export\Generator::MAX_FILE_SIZE) {
                $size += filesize($path);
            }
        }

        return $size;
    }

    /**
     * Get allowed pack types
     *
     * @return array
     */
    protected function getAllowedPackTypes()
    {
        return $this->getGenerator()->getAllowedArchives();
    }
}

