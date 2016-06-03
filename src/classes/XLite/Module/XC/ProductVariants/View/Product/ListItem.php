<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */


namespace XLite\Module\XC\ProductVariants\View\Product;

use XLite\Model\Cart;
use XLite\Module\XC\ProductVariants\Model\Product\ProductVariantsStockAvailabilityPolicy;

/**
 * Product list item widget
 */
class ListItem extends \XLite\View\Product\ListItem implements \XLite\Base\IDecorator
{
    /**
     * Get cache parameters
     *
     * @return array
     */
    protected function getCacheParameters()
    {
        $list = parent::getCacheParameters();

        /** @var ProductVariantsStockAvailabilityPolicy $policy */
        $policy = $this->getParam(self::PARAM_PRODUCT_STOCK_AVAILABILITY_POLICY);
        $cart   = Cart::getInstance();

        $list[] = $policy->getFirstAvailableVariantId($cart);

        return $list;
    }
}