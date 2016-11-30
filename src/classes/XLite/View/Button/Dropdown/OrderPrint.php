<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Button\Dropdown;

/**
 * Order print
 */
class OrderPrint extends \XLite\View\Button\Dropdown\ADropdown
{
    /**
     * Define additional buttons
     *
     * @return array
     */
    protected function defineAdditionalButtons()
    {
        return [
            'invoice' => [
                'class' => 'XLite\View\Button\PrintSelectedInvoices',
                'params'   => [
                    'label'      => 'Print invoice',
                    'style'      => 'action link list-action',
                    'icon-style' => 'fa fa-print',
                ],
                'position' => 100,
            ],
            'packingSlip' => [
                'class' => 'XLite\View\Button\PrintSelectedPackingSlip',
                'params'   => [
                    'label'      => 'Print packing slip',
                    'style'      => 'action link list-action',
                    'icon-style' => 'fa fa-print',
                ],
                'position' => 200,
            ],
        ];
    }
}
