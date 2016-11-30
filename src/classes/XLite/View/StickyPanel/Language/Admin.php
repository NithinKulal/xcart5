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
     * Define additional buttons
     *
     * @return array
     */
    protected function defineAdditionalButtons()
    {
        $list = parent::defineAdditionalButtons();

        $list['find'] = [
            'class'    => 'XLite\View\Button\Dropdown\LanguageActions',
            'params'   => [
                'disabled'       => false,
                'label'          => 'Add new language',
                'style'          => 'more-action always-enabled',
                'useCaretButton' => false,
                'dropDirection'  => 'dropup',
            ],
            'position' => 100,
        ];

        return $list;
    }
}
