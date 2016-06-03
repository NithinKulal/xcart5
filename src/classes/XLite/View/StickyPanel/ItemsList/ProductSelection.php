<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\StickyPanel\ItemsList;

/**
 * Product selection items list's sticky panel
 */
class ProductSelection extends \XLite\View\StickyPanel\ItemsListForm
{
    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'js/stickyPanelProductSelection.js';

        return $list;
    }

    /**
     * Get class
     *
     * @return string
     */
    protected function getClass()
    {
        $class = parent::getClass();
        $class .= ' product-selection-sticky-panel';

        return $class;
    }

    /**
     * Defines the label for the save button
     *
     * @return string
     */
    protected function getSaveWidgetLabel()
    {
        return static::t('Add products');
    }

    /**
     * Defines the style for the save button
     *
     * @return string
     */
    protected function getSaveWidgetStyle()
    {
        return parent::getSaveWidgetStyle() . ' more-action';
    }
}
