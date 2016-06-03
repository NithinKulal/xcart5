<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Sale\View\Model;

/**
 * Product model widget extention
 *
 * @Decorator\Depend("CDev\MarketPrice")
 */
class ProductMarketPrice extends \XLite\View\Model\Product implements \XLite\Base\IDecorator
{
    /**
     * Define the field after which the sale field will be inserted (if MarketPrice is switch on - marketPrice)
     *
     * @return string
     */
    protected function getSchemaIdToSeek()
    {
        return 'marketPrice';
    }
}
