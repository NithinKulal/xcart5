<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductVariants\View;

/**
 * Product price
 */
class Price extends \XLite\View\Price implements \XLite\Base\IDecorator
{
    /**
     * Return net price of product
     *
     * @return float
     */
    protected function getNetPrice($value = null)
    {
        return $this->getProductVariant()
            ? $this->getProductVariant()->getDisplayPrice()
            : parent::getNetPrice($value);
    }
}
