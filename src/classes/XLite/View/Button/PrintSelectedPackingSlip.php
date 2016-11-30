<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Button;

/**
 * 'Print selected packing slip' button
 */
class PrintSelectedPackingSlip extends \XLite\View\Button\PrintSelectedInvoices
{
    /**
     * Return URL params to use with onclick event
     *
     * @return array
     */
    protected function getURLParams()
    {
        return array(
            'url_params' => array (
                'target'    => 'order',
                'mode'      => 'packing_slip',
                'order_ids' => '',
            ),
        );
    }
}
