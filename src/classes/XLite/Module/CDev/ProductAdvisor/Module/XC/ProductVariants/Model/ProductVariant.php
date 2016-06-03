<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\ProductAdvisor\Module\XC\ProductVariants\Model;

/**
 * Product variant
 * @Decorator\Depend("XC\ProductVariants")
 *
 */
class ProductVariant extends \XLite\Module\XC\ProductVariants\Model\ProductVariant implements \XLite\Base\IDecorator
{
    /**
     * Check if the product is out-of-stock
     *
     * @return boolean
     */
    public function isShowStockWarning()
    {
        return $this->getProduct() && $this->getProduct()->isUpcomingProduct()
            ? false
            : parent::isShowStockWarning();
    }
}
