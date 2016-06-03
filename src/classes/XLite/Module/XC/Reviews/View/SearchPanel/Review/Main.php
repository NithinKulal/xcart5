<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Reviews\View\SearchPanel\Review;

/**
 * Main admin reviews list search panel
 */
class Main extends \XLite\View\SearchPanel\ASearchPanel
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

        $list[] = 'modules/XC/Reviews/search_panel/review/style.css';

        return $list;
    }

    /**
     * Define the items list CSS class with which the search panel must be linked
     *
     * @return string
     */
    protected function getLinkedItemsList()
    {
        return parent::getLinkedItemsList() . '.widget.items-list.reviews';
    }

    /**
     * Get form class
     *
     * @return string
     */
    protected function getFormClass()
    {
        return '\XLite\Module\XC\Reviews\View\Form\ReviewsSearch';
    }

    /**
     * Define conditions
     *
     * @return array
     */
    protected function defineConditions()
    {
        return parent::defineConditions() + array(
            'keywords' => array(
                static::CONDITION_CLASS => 'XLite\View\FormField\Input\Text',
                \XLite\View\FormField\AFormField::PARAM_FIELD_ONLY  => true,
                \XLite\View\FormField\Input\Text::PARAM_PLACEHOLDER => static::t('Product, SKU or customer info'),
            ),
            'rating' => array(
                static::CONDITION_CLASS => '\XLite\Module\XC\Reviews\View\FormField\Select\ReviewRating',
                \XLite\View\FormField\AFormField::PARAM_FIELD_ONLY => true,
            ),
            'type' => array(
                static::CONDITION_CLASS => '\XLite\Module\XC\Reviews\View\FormField\Select\ReviewType',
                \XLite\View\FormField\AFormField::PARAM_FIELD_ONLY => true,
            ),
            'status' => array(
                static::CONDITION_CLASS => '\XLite\Module\XC\Reviews\View\FormField\Select\ReviewStatus',
                \XLite\View\FormField\AFormField::PARAM_FIELD_ONLY => true,
            ),
            'dateRange' => array(
                static::CONDITION_CLASS => '\XLite\View\FormField\Input\Text\DateRange',
                \XLite\View\FormField\AFormField::PARAM_FIELD_ONLY  => true,
                \XLite\View\FormField\Input\Text::PARAM_PLACEHOLDER => static::t('Enter date range'),
            ),
        );
    }
}
