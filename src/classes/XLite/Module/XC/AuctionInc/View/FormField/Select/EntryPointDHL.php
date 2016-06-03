<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\AuctionInc\View\FormField\Select;

/**
 * DHL entry point selector
 */
class EntryPointDHL extends AEntryPoint
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
            'D'                    => 'Drop Off',
            'P'                    => 'Daily Pickup',
            'T1'                   => 'One Time Pickup / Web',
            'T2'                   => 'One Time Pickup / Phone',
        );
    }
}
