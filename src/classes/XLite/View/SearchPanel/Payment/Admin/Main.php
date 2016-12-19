<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\SearchPanel\Payment\Admin;

/**
 * Main admin payment search panel
 */
class Main extends \XLite\View\SearchPanel\Payment\Admin\AAdmin
{
    /**
     * Get form class
     *
     * @return string
     */
    protected function getFormClass()
    {
        return 'XLite\View\Form\Payment\Method\Admin\Search';
    }

    /**
     * Define the items list CSS class with which the search panel must be linked
     *
     * @return string
     */
    protected function getLinkedItemsList()
    {
        return parent::getLinkedItemsList() . '.widget.items-list.payment-methods';
    }

    /**
     * Get JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = 'form_field/country_autocomplete.js';

        return $list;
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
                \XLite\View\FormField\Input\Text::PARAM_PLACEHOLDER => static::t('Search payment method'),
                \XLite\View\FormField\AFormField::PARAM_FIELD_ONLY => true,
            ),
            'country' => array(
                static::CONDITION_CLASS => 'XLite\View\FormField\Select\Country',
                \XLite\View\FormField\AFormField::PARAM_FIELD_ONLY => true,
                \XLite\View\FormField\Select\Country::PARAM_ALL => true,
                \XLite\View\FormField\Select\Country::PARAM_SELECT_ONE => true,
                \XLite\View\FormField\Select\Country::PARAM_SELECT_ONE_LABEL => static::t('All countries'),
            ),
        );
    }
}
