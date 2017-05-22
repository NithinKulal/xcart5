<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\NotFinishedOrders\Controller\Customer;

/**
 * Web-based payment method return
 */
abstract class PaymentReturn extends \XLite\Controller\Customer\PaymentReturn implements \XLite\Base\IDecorator
{
    /**
     * Updates order state by transaction
     *
     * @param \XLite\Model\Payment\Transaction $txn Processed payment transaction
     *
     * @return void
     */
    public function updateOrderState($txn)
    {
        parent::updateOrderState($txn);

        if ($txn->getOrder()
            && ($txn->getStatus() == \XLite\Model\Payment\Transaction::STATUS_FAILED
            || $txn->getStatus() == \XLite\Model\Payment\Transaction::STATUS_CANCELED)) {
            $txn->getOrder()->setPaymentStatusByTransaction($txn);
        }
    }
}
