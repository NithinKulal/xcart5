<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductVariants\Module\CDev\Sale\View;

/**
 * Product price
 * @Decorator\Depend("CDev\Sale")
 *
 */
class Price extends \XLite\View\Price implements \XLite\Base\IDecorator
{
    /**
     * Return old price value
     *
     * @return float
     */
    protected function getOldPrice()
    {
        return $this->getProductVariant()
            ? $this->getProductVariant()->getDisplayPriceBeforeSale()
            : parent::getOldPrice();
    }
}