<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\MarketPrice\Module\CDev\Sale\Model;

/**
 * Class Product
 *
 * @Decorator\Depend("CDev\Sale")
 */
class Product extends \XLite\Model\Product implements \XLite\Base\IDecorator
{
    /**
     * Return old display product price (before sale)
     *
     * @return float
     */
    public function getDisplayPriceBeforeSale()
    {
        return max(\XLite\Module\CDev\Sale\Logic\PriceBeforeSale::getInstance()->apply($this, 'getNetPriceBeforeSale', array('taxable'), 'display'), $this->getMarketPrice());
    }
}