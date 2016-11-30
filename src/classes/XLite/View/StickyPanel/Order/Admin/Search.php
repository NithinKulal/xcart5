<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\StickyPanel\Order\Admin;

/**
 * Search order list sticky panel
 */
class Search extends \XLite\View\StickyPanel\Order\Admin\AAdmin
{
    /**
     * Define additional buttons
     *
     * @return array
     */
    protected function defineAdditionalButtons()
    {
        return [
            'delete' => [
                'class'    => 'XLite\View\Button\DeleteSelected',
                'params'   => [
                    'label'      => '',
                    'style'      => 'more-action icon-only hide-on-disable hidden',
                    'icon-style' => 'fa fa-trash-o',
                ],
                'position' => 100,
            ],
            'print'  => [
                'class'    => 'XLite\View\Button\Dropdown\OrderPrint',
                'params'   => [
                    'label'         => '',
                    'style'         => 'more-action icon-only hide-on-disable hidden',
                    'icon-style'    => 'fa fa-print',
                    'showCaret'     => false,
                    'dropDirection' => 'dropup',
                ],
                'position' => 200,
            ],
        ];
    }
}
