<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Sale\View;

/**
 * Items list form button
 */
abstract class ItemsListForm extends \XLite\View\StickyPanel\Product\Admin\Search implements \XLite\Base\IDecorator
{
    /**
     * Define additional buttons
     *
     * @return array
     */
    protected function defineAdditionalButtons()
    {
        $list = parent::defineAdditionalButtons();
        $list['sale'] = [
            'class'    => 'XLite\Module\CDev\Sale\View\Button\Dropdown\ProductSale',
            'params'   => [
                'label'         => '',
                'style'         => 'more-action icon-only hide-on-disable hidden',
                'icon-style'    => 'fa fa-percent',
                'showCaret'     => false,
                'dropDirection' => 'dropup',
            ],
            'position' => 250,
        ];

        return $list;
    }
}
