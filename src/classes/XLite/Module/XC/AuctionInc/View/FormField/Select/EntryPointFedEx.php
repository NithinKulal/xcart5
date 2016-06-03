<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\AuctionInc\View\FormField\Select;

/**
 * FedEx entry point selector
 */
class EntryPointFedEx extends AEntryPoint
{
    /**
     * Get default options for selector
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return array(
            static::STATE_DISABLED => 'Disable carrier',
            'B'                    => 'Business Service Center',
            'C1'                   => 'Request Courier Same Day Phone',
            'C2'                   => 'Request Courier Future Day Phone',
            'C3'                   => 'Request Courier Same Day Electronic',
            'C4'                   => 'Request Courier Future Day Electronic',
            'D'                    => 'Drop Box',
            'R'                    => 'Regular Pickup',
            'S'                    => 'Station',
        );
    }
}
