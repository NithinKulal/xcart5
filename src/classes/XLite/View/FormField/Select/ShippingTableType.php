<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Select;

/**
 * Shipping table type
 */
class ShippingTableType extends \XLite\View\FormField\Select\Regular
{
    /**
     * getDefaultOptions
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return array(
            'S'   => static::t('shippingTableType.Subtotal'),
            'W'   => static::t('shippingTableType.Weight'),
            'I'   => static::t('shippingTableType.Items'),
            'WSI' => static::t('shippingTableType.All'),
        );
    }
}
