<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\View\Button\Product;

/**
 * Express Checkout button
 */
class ExpressCheckout extends \XLite\Module\CDev\Paypal\View\Button\AExpressCheckout
{
    /**
     * Returns true if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && \XLite\Module\CDev\Paypal\Main::isBuyNowEnabled();
    }

    /**
     * Returns widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/CDev/Paypal/button/product/default/express_checkout.twig';
    }

    /**
     * Return current template
     *
     * @return string
     */
    protected function getTemplate()
    {
        return $this->isInContextAvailable()
            ? 'modules/CDev/Paypal/button/product/in_context/express_checkout.twig'
            : 'modules/CDev/Paypal/button/product/default/express_checkout.twig';
    }
}
