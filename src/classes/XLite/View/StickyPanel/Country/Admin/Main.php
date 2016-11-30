<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\StickyPanel\Country\Admin;

/**
 * Countries
 */
class Main extends \XLite\View\StickyPanel\ItemsListForm
{
    /**
     * Define additional buttons
     *
     * @return array
     */
    protected function defineAdditionalButtons()
    {
        return [
            'status' => [
                'class'    => 'XLite\View\Button\Dropdown\Status',
                'params'   => [
                    'label'          => static::t('Status'),
                    'style'          => 'more-action hide-on-disable hidden',
                    'icon-style'     => 'fa fa-power-off',
                    'showCaret'      => true,
                    'useCaretButton' => false,
                    'dropDirection'  => 'dropup',
                ],
                'position' => 200,
            ],
        ];
    }
}
