<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\Amazon\PayWithAmazon\Controller\Admin;

use XLite\Core\Request;
use XLite\Module\Amazon\PayWithAmazon\Main;

/**
 * PayWithAmazon settings page controller
 */
class PayWithAmazon extends \XLite\Controller\Admin\PaymentMethod
{
    /**
     * getPaymentMethod
     *
     * @return \XLite\Model\Payment\Method
     */
    protected function getPaymentMethod()
    {
        $method                           = Main::getMethod();
        Request::getInstance()->method_id = $method->getMethodId();

        return $method;
    }

    /**
     * Update payment method
     *
     * @return void
     */
    protected function doActionUpdate()
    {
        parent::doActionUpdate();

        $method = $this->getPaymentMethod();
        if ($method && $method->isConfigured()) {
            $this->setReturnURL($this->buildURL('pay_with_amazon'));
        }
    }
}
