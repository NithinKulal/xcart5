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
        $list = parent::defineAdditionalButtons();

        $list[] = $this->getWidget(
            array(
                'disabled'   => true,
                'label'      => 'Delete',
                'style'      => 'more-action link list-action',
                'icon-style' => 'fa fa-trash-o',
            ),
            'XLite\View\Button\DeleteSelected'
        );

        $list[] = $this->getWidget(
            array(),
            'XLite\View\Button\Divider'
        );

        $list[] = $this->getWidget(
            array(
                'disabled'   => true,
                'label'      => 'Print invoice',
                'style'      => 'more-action',
                'icon-style' => 'fa fa-print',
            ),
            'XLite\View\Button\PrintSelectedInvoices'
        );

        $list[] = $this->getWidget(
            array(
                'disabled'   => true,
                'label'      => 'Print packing slip',
                'style'      => 'more-action',
                'icon-style' => 'fa fa-print',
            ),
            'XLite\View\Button\PrintSelectedPackingSlip'
        );

        return $list;
    }
}
