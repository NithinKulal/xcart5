<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Select;

/**
 * Decimals selector
 */
class Decimals extends \XLite\View\FormField\Select\Regular
{
    /**
     * Get default options
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return array(
            0 => 0,
            1 => 1,
            2 => 2,
            3 => 3,
            4 => 4
        );
    }
}
