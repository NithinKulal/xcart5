<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Sale\View\Button\Dropdown;

/**
 * Product status
 */
class ProductSale extends \XLite\View\Button\Dropdown\ADropdown
{
    /**
     * Define additional buttons
     *
     * @return array
     */
    protected function defineAdditionalButtons()
    {
        return [
            'sale' => [
                'class'    => 'XLite\Module\CDev\Sale\View\SaleSelectedButton',
                'params'   => [
                    'label'      => 'Put up for sale',
                    'style'      => 'action link list-action',
                    'icon-style' => 'fa fa-percent state-on',
                ],
                'position' => 100,
            ],
            'cancel' => [
                'params'   => [
                    'action'     => 'sale_cancel_sale',
                    'label'      => 'Cancel sale',
                    'style'      => 'action link list-action',
                    'icon-style' => 'fa fa-percent state-off',
                ],
                'position' => 200,
            ],
        ];
    }
}
