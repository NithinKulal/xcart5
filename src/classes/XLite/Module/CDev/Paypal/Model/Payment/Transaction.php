<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\Model\Payment;

use \XLite\Module\CDev\Paypal;

/**
 * Payment transaction
 */
class Transaction extends \XLite\Model\Payment\Transaction implements \XLite\Base\IDecorator
{
    /**
     * Check if transaction by Paypal payment method
     *
     * @return boolean
     */
    public function isByPayPal()
    {
        /** @var \XLite\Model\Payment\Method $paymentMethod */
        $paymentMethod = $this->getPaymentMethod();
        return in_array($paymentMethod->getServiceName(), Paypal\Main::getServiceCodes(), true);
    }
}
