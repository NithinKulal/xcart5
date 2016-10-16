<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\Controller\Customer;

/**
 * Web-based payment method return
 */
class PaymentReturn extends \XLite\Controller\Customer\PaymentReturn implements \XLite\Base\IDecorator
{
    protected $reload = false;

    /**
     * @var \XLite\Model\Payment\Transaction
     */
    protected $transaction;

    /**
     * Set return URL
     *
     * @param string $url URL to set
     *
     * @return void
     */
    public function setReturnURL($url)
    {
        if (\XLite\Module\CDev\Paypal\Main::isExpressCheckoutEnabled()
            && \XLite\Module\CDev\Paypal\Main::isInContextCheckoutAvailable()
            && \XLite\Core\Request::getInstance()->cancelUrl
        ) {
            $url = $this->getShopURL(
                \XLite\Core\Request::getInstance()->cancelUrl,
                \XLite\Core\Config::getInstance()->Security->customer_security
            );
        }

        parent::setReturnURL($url);
    }
}
