<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CanadaPost\View\SearchPanel\ProductsReturn\Admin;

/**
 * Main admin orders list search panel
 */
class Main extends \XLite\Module\XC\CanadaPost\View\SearchPanel\ProductsReturn\Admin\AAdmin
{
    /**
     * Via this method the widget registers the CSS files which it uses.
     * During the viewers initialization the CSS files are collecting into the static storage.
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/XC/CanadaPost/search_panel/return/style.css';

        return $list;
    }

    /**
     * Get form class
     *
     * @return string
     */
    protected function getFormClass()
    {
        return '\XLite\Module\XC\CanadaPost\View\Form\ProductsReturn\Search';
    }

    /**
     * Define the items list CSS class with which the search panel must be linked
     *
     * @return string
     */
    protected function getLinkedItemsList()
    {
        return parent::getLinkedItemsList() . '.widget.items-list.capost-returns-list';
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
                \XLite\View\FormField\AFormField::PARAM_FIELD_ONLY  => true,
                \XLite\View\FormField\Input\Text::PARAM_PLACEHOLDER => static::t('Enter Return # or Order #'),
            ),
            'status' => array(
                static::CONDITION_CLASS => '\XLite\Module\XC\CanadaPost\View\FormField\Select\ReturnStatus',
                \XLite\View\FormField\AFormField::PARAM_FIELD_ONLY  => true,
                \XLite\Module\XC\CanadaPost\View\FormField\Select\ReturnStatus::PARAM_ALL_OPTION  => true,
            ),
            'dateRange' => array(
                static::CONDITION_CLASS => '\XLite\View\FormField\Input\Text\DateRange',
                \XLite\View\FormField\AFormField::PARAM_FIELD_ONLY  => true,
            ),
        );
    }
}
