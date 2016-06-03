<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Select;

/**
 * Product class with new selector
 */
class ProductClassWithNew extends \XLite\View\FormField\Select\ProductClass
{
    /**
     * Get default options
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return parent::getDefaultOptions() + array(-1 => 'New product class');
    }
}
