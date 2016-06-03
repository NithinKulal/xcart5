<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\FedEx\View\FormField\Select;

/**
 * RateRequestType selector for settings page
 */
class RateRequestType extends \XLite\View\FormField\Select\Regular
{
    /**
     * Get default options for selector
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return array(
            'LIST'          => 'LIST — Returns published rates',
            'PREFERRED'     => 'PREFERRED — Returns rates in currency specified in the PreferredCurrency element.',
            'NONE'          => 'NONE - Returns account rates in response',
        );
    }
}
