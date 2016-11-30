<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductVariants\View\Product\Details\Customer;

/**
 * Quantity widget
 */
class Quantity extends \XLite\View\Product\Details\Customer\Quantity implements \XLite\Base\IDecorator
{
    /**
     * Return maximum allowed quantity
     *
     * @return integer
     */
    protected function getMaxQuantity()
    {
        $productVariant = $this->getProductVariant();

        return $productVariant
            ? $productVariant->getAvailableAmount()
            : parent::getMaxQuantity();
    }
}
