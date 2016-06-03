<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Input\Text;

/**
 * Weight
 */
class Weight extends \XLite\View\FormField\Input\Text\FloatInput
{
    /**
     * Get default E
     *
     * @return integer
     */
    static protected function getDefaultE()
    {
        return 4;
    }
}
