<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\AuctionInc\View\FormField\Select;

/**
 * USPS entry point selector
 */
class EntryPointUSPS extends AEntryPoint
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
            ''                     => 'Not specified',
            'R'                    => 'Retail Rate',
            'C'                    => 'Commercial Base Rates (when available)',
            'O'                    => 'Commercial Base Rates Only',
        );
    }
}
