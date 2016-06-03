<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\SearchPanel\Label;

/**
 * Main language labels search panel
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

        $list[] = 'search_panel/label/style.css';

        return $list;
    }

    /**
     * Get form class
     *
     * @return string
     */
    protected function getFormClass()
    {
        return '\XLite\View\Form\Translations\LabelSearch';
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
                \XLite\View\FormField\Input\Text::PARAM_PLACEHOLDER => static::t('Enter search pattern'),
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
        $list = parent::defineActions();

        $list['import_language'] = array(
            'class' => '\XLite\View\Button\FileSelector',
            \XLite\View\Button\AButton::PARAM_LABEL => static::t('Import language from CSV file'),
            \XLite\View\Button\FileSelector::PARAM_OBJECT => 'language',
            \XLite\View\Button\FileSelector::PARAM_FILE_OBJECT => 'file',
        );

        $list['add_label'] = array(
            'class' => '\XLite\View\LanguagesModify\Button\AddNewLabel',
            \XLite\View\Button\AButton::PARAM_LABEL => static::t('Add new label'),
            \XLite\View\LanguagesModify\Button\AddNewLabel::PARAM_LANGUAGE => \XLite\Core\Request::getInstance()->code,
        );

        return $list;
    }
}
