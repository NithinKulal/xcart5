<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Select;

/**
 * Select "DefaultDisplayModeForProductsList"
 */
class DefaultDisplayModeForProductsList extends \XLite\View\FormField\Select\Regular
{
    /**
     * getDefaultOptions
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        $modes = \XLite\View\ItemsList\Product\Customer\ACustomer::getCenterDisplayModes();

        $options = array();

        foreach ($modes as $key => $mode) {
            $options[$key] = static::t($mode);
        }

        return $options;
    }
}
