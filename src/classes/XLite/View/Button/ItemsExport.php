<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Button;

/**
 * ItemsExport button
 */
abstract class ItemsExport extends \XLite\View\Button\AButton
{
    /**
     * getJSFiles
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'button/js/items_export.js';

        return $list;
    }

    /**
     * Register CSS files for delete address button
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'button/css/items_export.css';

        return $list;
    }

    abstract protected function getAdditionalButtons();

    /**
     * Get attributes
     *
     * @return boolean
     */
    protected function isMultipleOptions()
    {
        return 1 < count($this->getAdditionalButtons());
    }

    /**
     * Get attributes
     *
     * @return boolean
     */
    protected function getFirstProviderLabel()
    {
        return key($this->getAdditionalButtons());
    }

    /**
     * getDefaultLabel
     *
     * @return string
     */
    protected function getDefaultLabel()
    {
        return static::t('Export all');
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'button/items_export.twig';
    }

    /**
     * Defines CSS class for widget to use in templates
     *
     * @return string
     */
    protected function getClass()
    {
        return parent::getClass() . ' items-export';
    }

    /**
     * Get commented data
     *
     * @return array
     */
    protected function getCommentedData()
    {
        return [
            'Export all'      => static::t('Export all'),
            'Export selected' => static::t('Export selected'),
        ];
    }
}
