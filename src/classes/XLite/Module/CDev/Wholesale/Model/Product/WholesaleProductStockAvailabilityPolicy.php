<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */


namespace XLite\Module\CDev\Wholesale\Model\Product;


use XLite\Controller\Customer\ACustomer;
use XLite\Model\Cart;
use XLite\Model\Product;


class WholesaleProductStockAvailabilityPolicy extends \XLite\Model\Product\ProductStockAvailabilityPolicy implements \XLite\Base\IDecorator
{
    const PRODUCT_MIN_QUANTITY = 'min_quantity';

    public function isOutOfStock(Cart $cart)
    {
        return parent::isOutOfStock($cart)
               || $this->getAvailableAmount($cart) < $this->dto[self::PRODUCT_MIN_QUANTITY];
    }

    protected function createDTO(Product $product)
    {
        $controller = \XLite::getController();

        $membership = $controller instanceof ACustomer
            ? ($controller->getCart()->getProfile() ? $controller->getCart()->getProfile()->getMembership() : null)
            : null;

        return parent::createDTO($product) + [
            self::PRODUCT_MIN_QUANTITY => $product->getMinQuantity($membership),
        ];
    }
}