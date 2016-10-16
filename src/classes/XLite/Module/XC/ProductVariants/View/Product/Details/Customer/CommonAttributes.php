<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductVariants\View\Product\Details\Customer;

/**
 * Product attributes
 */
class CommonAttributes extends \XLite\View\Product\Details\Customer\CommonAttributes implements \XLite\Base\IDecorator
{
    /**
     * Return SKU of product
     *
     * @return string
     */
    protected function getSKU()
    {
        return $this->getProductVariant()
            ? $this->getProductVariant()->getDisplaySku()
            : parent::getSKU();
    }

    /**
     * Get cache parameters
     *
     * @return array
     */
    protected function getCacheParameters()
    {
        $list = parent::getCacheParameters();

        $variantId = $this->getProductVariant() ? $this->getProductVariant()->getId() : null;
        $list[] = $variantId;

        return $list;
    }

    /**
     * Return weight of product
     *
     * @return float
     */
    protected function getClearWeight()
    {
        return $this->getProductVariant()
            ? $this->getProductVariant()->getClearWeight()
            : parent::getClearWeight();
    }
}
