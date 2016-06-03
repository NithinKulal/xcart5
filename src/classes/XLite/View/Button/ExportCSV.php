<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Button;

/**
 * Submit button for export products
 *
 */
class ExportCSV extends \XLite\View\Button\APopupButton
{
    /**
     * Widget params
     */
    const PARAM_ENTITY = 'entity';
    const PARAM_SESSION_CELL = 'session';

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        return array_merge(
            parent::getJSFiles(),
            array(
                'event_task_progress/controller.js',
                'export/controller_popup.js',
                'button/js/export_csv.js',
            )
        );
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
            static::PARAM_ENTITY => new \XLite\Model\WidgetParam\TypeString('Exported entity class', ''),
            static::PARAM_SESSION_CELL => new \XLite\Model\WidgetParam\TypeString('Export condition session cell name', ''),
        );
    }

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        return array_merge(
            parent::getCSSFiles(),
            array(
                'event_task_progress/style.css',
                'export/style.css'
            )
        );
    }

    /**
     * Return URL parameters to use in AJAX popup
     *
     * @return array
     */
    protected function prepareURLParams()
    {
        return array(
            'target' => 'export',
            'widget' => 'XLite\View\PopupExport',
            'exportReturnURL' => \XLite\Core\URLManager::getCurrentURL(),
        );
    }

    /**
     * Return array of URL params for JS
     *
     * @return array
     */
    public function getURLParams()
    {
        $params = parent::getURLParams();
        $params['export'] = array(
            'target' => 'export',
            'action' => 'itemlist_export',
            'section' => array(
                $this->getExportEntity()
            ),
            'options' => array(
                'charset' => \XLite\Core\Config::getInstance()->Units->export_import_charset,
                'attrs' => 'global',
                'files' => 'local',
                'filter' => $this->getExportSessionCell()
            )
        );

        return $params;
    }

    /**
     * getDefaultLabel
     *
     * @return string
     */
    protected function getExportEntity()
    {
        return $this->getParam(static::PARAM_ENTITY);
    }

    /**
     * getDefaultLabel
     *
     * @return string
     */
    protected function getExportSessionCell()
    {
        return $this->getParam(static::PARAM_SESSION_CELL);
    }

    /**
     * getDefaultLabel
     *
     * @return string
     */
    protected function getDefaultLabel()
    {
        return 'CSV';
    }

    /**
     * getClass
     *
     * @return string
     */
    protected function getClass()
    {
        return parent::getClass() . ' export-csv always-reload';
    }
}
