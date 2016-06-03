<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\View\Checkout;

/**
 * Payment template
 */
abstract class Payment extends \XLite\View\Checkout\Payment implements \XLite\Base\IDecorator
{
    /**
     * Get JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        if ((!$this->isTokenValid()
                || \XLite\Module\CDev\Paypal\Model\Payment\Processor\ExpressCheckout::EC_TYPE_SHORTCUT
                    !== \XLite\Core\Session::getInstance()->ec_type
            )
            && \XLite\Module\CDev\Paypal\Main::isExpressCheckoutEnabled()
            && \XLite\Module\CDev\Paypal\Main::isInContextCheckoutAvailable()
        ) {
            $list[] = 'modules/CDev/Paypal/checkout/payment.js';
        }

        $method = \XLite\Core\Database::getRepo('XLite\Model\Payment\Method')
            ->findOneBy(array('service_name' => 'PayflowTransparentRedirect'));
        if ($method && $method->isEnabled()) {
            $list[] = 'modules/CDev/Paypal/transparent_redirect/payment.js';
        }

        return $list;
    }

    /**
     * Get CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $method = \XLite\Core\Database::getRepo('XLite\Model\Payment\Method')
            ->findOneBy(array('service_name' => 'PayflowTransparentRedirect'));
        if ($method && $method->isEnabled()) {
            //Add CSS file for dynamic credit card widget
            $list = array_merge($list, $this->getWidget(array(), 'XLite\View\CreditCard')->getCSSFiles());
        }

        return $list;
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
}
