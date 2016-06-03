<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\USPS\View\FormField\Select;

/**
 * Package size selector for settings page
 */
class PackageSize extends \XLite\View\FormField\Select\Regular
{
    /**
     * Get default options for selector
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return array(
            'REGULAR' => 'Regular (package dimensions are 12" or less)',
            'LARGE'   => 'Large (any package dimension is larger than 12")',
        );
    }
}
