<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\AuctionInc\View\FormField\Select;

/**
 * UPR entry point selector
 */
class EntryPointUPS extends AEntryPoint
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
            'D'                    => 'Drop-off',
            'O5'                   => 'On Call/Pickup Residential Same Day',
            'O6'                   => 'On Call/Pickup Residential Future Day',
            'O7'                   => 'On Call/Pickup Commercial Same Day',
            'O8'                   => 'On Call/Pickup Commerical Future Day',
            'P'                    => 'Daily Pickup (UPS Account started before 01-02-11)',
            'Q'                    => 'Daily Pickup (UPS Account started after 01-02-11)',
            'R'                    => 'Retail Location',
        );
    }
}
