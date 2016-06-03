<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Inline\Input\Text\Price;

/**
 * Product price
 */
class Product extends \XLite\View\FormField\Inline\Input\Text\Price
{
    /**
     * Get initial field parameters
     *
     * @param array $field Field data
     *
     * @return array
     */
    protected function getFieldParams(array $field)
    {
        return parent::getFieldParams($field) + array('min' => 0);
    }

}
