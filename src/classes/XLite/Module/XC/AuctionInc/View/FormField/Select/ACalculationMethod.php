<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\AuctionInc\View\FormField\Select;

/**
 * Calculation method selector
 */
abstract class ACalculationMethod extends \XLite\View\FormField\Select\Regular
{
    /**
     * Get default options for selector
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return array(
            'C'  => 'Carrier Rates',
            'F'  => 'Fixed Fee',
            'N'  => 'Free',
            'CI' => 'Free Domestic'
        );
    }
}
