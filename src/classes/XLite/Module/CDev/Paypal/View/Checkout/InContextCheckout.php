<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\View\Checkout;

/**
 * Payment widget
 */
class InContextCheckout extends \XLite\View\AView
{
    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/CDev/Paypal/checkout/in_context_checkout/body.twig';
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && (
                !$this->isTokenValid()
                || \XLite\Module\CDev\Paypal\Model\Payment\Processor\ExpressCheckout::EC_TYPE_SHORTCUT
                    !== \XLite\Core\Session::getInstance()->ec_type
            )
            && \XLite\Module\CDev\Paypal\Main::isExpressCheckoutEnabled()
            && \XLite\Module\CDev\Paypal\Main::isInContextCheckoutAvailable();
    }

    /**
     * Returns true if token initialized and is not expired
     *
     * @return boolean
     */
    protected function isTokenValid()
    {
        return !empty(\XLite\Core\Session::getInstance()->ec_token)
            && \XLite\Module\CDev\Paypal\Model\Payment\Processor\ExpressCheckout::TOKEN_TTL
                > \XLite\Core\Converter::time() - \XLite\Core\Session::getInstance()->ec_date
            && !empty(\XLite\Core\Session::getInstance()->ec_payer_id);
    }

    /**
     * Get popup url
     *
     * @return string
     */
    protected function getPopupUrl()
    {
        $params = array(
            'inContext' => true,
            'cancelUrl' => \XLite\Core\URLManager::getSelfURI(),
            'ignoreCheckout' => true,
        );

        return \XLite::getInstance()->getShopURL($this->buildURL('checkout', 'start_express_checkout', $params));
    }
}
