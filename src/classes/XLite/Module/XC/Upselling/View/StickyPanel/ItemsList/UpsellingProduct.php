<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Upselling\View\StickyPanel\ItemsList;

/**
 * U products items list's sticky panel
 */
class UpsellingProduct extends \XLite\View\StickyPanel\ItemsListForm
{
    /**
     * Define additional buttons
     *
     * @return array
     */
    protected function defineAdditionalButtons()
    {
        $list = parent::defineAdditionalButtons();
        $list['edit_all'] = [
            'class'    => 'XLite\Module\XC\Upselling\View\Button\EditUpsellingProducts',
            'params'   => [
                'style'          => 'more-action always-enabled edit-all',
                'useCaretButton' => false,
                'dropDirection'  => 'dropup',
            ],
            'position' => 50,
        ];

        return $list;
    }
}