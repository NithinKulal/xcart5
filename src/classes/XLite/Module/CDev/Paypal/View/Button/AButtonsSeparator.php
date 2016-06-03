<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\View\Button;

/**
 * Checkout buttons separator
 */
class AButtonsSeparator extends \XLite\View\Button\ButtonsSeparator
{
    /**
     * isExpressCheckoutEnabled 
     * 
     * @return boolean
     */
    protected function isVisible()
    {
        $cart = $this->getCart();

        return parent::isVisible()
            && $cart
            && (0 < $cart->getTotal())
            && $cart->checkCart()
            && \XLite\Module\CDev\Paypal\Main::isExpressCheckoutEnabled($cart);
    }
}
