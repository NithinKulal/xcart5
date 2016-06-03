<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\SearchPanel\Countries;

/**
 * Countries search panel
 */
class Main extends \XLite\View\SearchPanel\ASearchPanel
{
    /**
     * Get form class
     *
     * @return string
     */
    protected function getFormClass()
    {
        return '\XLite\View\Form\Countries\Search';
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
                static::CONDITION_CLASS                             => 'XLite\View\FormField\Input\Text',
                \XLite\View\FormField\AFormField::PARAM_FIELD_ONLY  => true,
                \XLite\View\FormField\Input\Text::PARAM_PLACEHOLDER => static::t('Enter pattern here'),
            ),
        );
    }

    /**
     * Define actions
     *
     * @return array
     */
    protected function defineActions()
    {
        $actions = parent::defineActions();

        $actions['submit'][\XLite\View\Button\AButton::PARAM_LABEL] = static::t('Find countries');

        return $actions;
    }
}
