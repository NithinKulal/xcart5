<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\SearchPanel\ProductSelections\Admin;

/**
 * Main admin orders list search panel
 */
class Main extends \XLite\View\SearchPanel\ProductSelections\Admin\AAdmin
{
    /**
     * Define the items list CSS class with which the search panel must be linked
     *
     * @return string
     */
    protected function getLinkedItemsList()
    {
        return parent::getLinkedItemsList() . '.widget.items-list.product_selections';
    }

    /**
     * Via this method the widget registers the CSS files which it uses.
     * During the viewers initialization the CSS files are collecting into the static storage.
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'search_panel/product_selections/style.css';

        return $list;
    }

    /**
     * Get form class
     *
     * @return string
     */
    protected function getFormClass()
    {
        return '\XLite\View\Form\ItemsList\ProductSelection\Search';
    }

    /**
     * Define conditions
     *
     * @return array
     */
    protected function defineConditions()
    {
        return parent::defineConditions() + array(
            'substring' => array(
                static::CONDITION_CLASS => 'XLite\View\FormField\Input\Text',
                \XLite\View\FormField\Input\Text::PARAM_PLACEHOLDER => static::t('Search keywords'),
                \XLite\View\FormField\AFormField::PARAM_FIELD_ONLY => true,
            ),
            'categoryId' => array(
                static::CONDITION_CLASS => 'XLite\View\FormField\Select\Category',
                \XLite\View\FormField\Select\Category::PARAM_DISPLAY_ANY_CATEGORY => true,
                \XLite\View\FormField\AFormField::PARAM_FIELD_ONLY => true,
            ),
        );
    }

}
