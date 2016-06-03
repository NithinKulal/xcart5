<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\VolumeDiscounts\View;

/**
 * Volume discounts promotion block widget in the cart
 *
 * @ListChild (list="checkout.review.selected", weight="14")
 * @ListChild (list="checkout.review.inactive", weight="14")
 */
class CheckoutPromo extends \XLite\Module\CDev\VolumeDiscounts\View\CartPromo
{
    /**
     * Return templates directory name
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/CDev/VolumeDiscounts/checkout_promo.twig';
    }
}
