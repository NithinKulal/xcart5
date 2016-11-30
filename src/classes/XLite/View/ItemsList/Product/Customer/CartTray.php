<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\ItemsList\Product\Customer;

/**
 * @ListChild (list="itemsList.product.cart", zone="customer")
 */
class CartTray extends \XLite\View\ASingleView
{
    /**
     * Cache availability
     *
     * @return boolean
     */
    protected function isCacheAvailable()
    {
        return true;
    }

    /**
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'items_list/product/cart_tray/cart-tray.twig';
    }
}