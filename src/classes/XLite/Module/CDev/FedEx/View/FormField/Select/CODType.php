<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\FedEx\View\FormField\Select;

/**
 * COD type selector for settings page
 */
class CODType extends \XLite\View\FormField\Select\Regular
{
    /**
     * Get default options for selector
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return array(
            'ANY'              => 'Any funds',
            'GUARANTEED_FUNDS' => 'Guaranteed funds',
            'CASH'             => 'Cash',
        );
    }
}
