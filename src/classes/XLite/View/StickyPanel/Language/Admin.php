<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\StickyPanel\Language;

/**
 * Language items list panel for admin interface
 */
class Admin extends \XLite\View\StickyPanel\ItemsListForm
{
    /**
     * Check panel has more actions buttons
     *
     * @return boolean 
     */
    protected function hasMoreActionsButtons()
    {
        return false;
    }

    /**
     * Returns "more actions" specific label
     * 
     * @return string
     */
    protected function getMoreActionsText()
    {
        return static::t('Add new language');
    }
    
    /**
     * Returns "more actions" specific label for bubble context window
     * 
     * @return string
     */
    protected function getMoreActionsPopupText()
    {
        return static::t('Add new language');
    }
 
    /**
     * Define additional buttons
     *
     * @return array
     */
    protected function defineAdditionalButtons()
    {
        $list = parent::defineAdditionalButtons();

        $list[] = $this->getWidget(
            array(
                'disabled' => false,
                'label'    => 'Find language in marketplace',
                'style'    => 'action link always-enabled',
                'location' => $this->buildURL('addons_list_marketplace', '', array('tag' => 'Translation')),
            ),
            'XLite\View\Button\Link'
        );

        $list[] = $this->getWidget(
            array(
                'disabled'   => false,
                'label'      => 'Import language from CSV file',
                'style'      => 'action link always-enabled',
                'object'     => 'language',
                'fileObject' => 'file',
            ),
            '\XLite\View\Button\FileSelector'
        );

        $list[] = $this->getWidget(
            array(
                'disabled' => false,
                'label'    => 'Add language',
                'style'    => 'action link always-enabled',
            ),
            'XLite\View\LanguagesModify\Button\AddNewLanguage'
        );

        return $list;
    }
}
