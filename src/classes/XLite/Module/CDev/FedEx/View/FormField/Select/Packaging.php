<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\FedEx\View\FormField\Select;

/**
 * Packaging selector for settings page
 */
class Packaging extends \XLite\View\FormField\Select\Regular
{
    /**
     * Get default options for selector
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return array(
            'YOUR_PACKAGING' => 'My packaging',
            'FEDEX_ENVELOPE' => 'FedEx Envelope',
            'FEDEX_PAK'      => 'FedEx Pak',
            'FEDEX_BOX'      => 'FedEx Box',
            'FEDEX_TUBE'     => 'FedEx Tube',
            'FEDEX_10KG_BOX' => 'FedEx 10Kg Box',
            'FEDEX_25KG_BOX' => 'FedEx 25Kg Box'
        );
    }
}
