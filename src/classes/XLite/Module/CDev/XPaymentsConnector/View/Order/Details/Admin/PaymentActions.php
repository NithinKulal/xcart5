<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\XPaymentsConnector\View\Order\Details\Admin;

/**
 * Payment actions unit widget (button capture or refund or void etc)
 */
class PaymentActions extends \XLite\View\Order\Details\Admin\PaymentActions implements \XLite\Base\IDecorator
{
    /**
     * Get transactions, and do some initialization
     *
     * @return array
     */
    protected function getTransactions()
    {
        $transactions = parent::getTransactions();

        if (!$this->allowedTransactions) {

            $allowedTransactions = array();

            foreach ($transactions as $transaction) {

                if (
                    $transaction->getPaymentMethod()
                    && $transaction->getPaymentMethod()->getProcessor()
                ) {

                    $processor = $transaction->getPaymentMethod()->getProcessor();

                    foreach ($processor->getAllowedTransactions() as $at) {

                        if (!in_array($at, $allowedTransactions)) {
                            $allowedTransactions[] = $at;
                        }
                    }

                }


            }

            $this->allowedTransactions = $allowedTransactions;

        }

        return $transactions;
    }

    /**
     * Public wrapper for getTransactionUnits()
     * ACCEPT and DECLINED actions are removed from the list, 
     * because are displayed in a different place
     *
     * @param \XLite\Model\Payment\Transaction $transaction Payment transaction
     *
     * @return array
     */
    public function getUnitsForTransaction($transaction = null)
    {
        $units = $this->getTransactionUnits($transaction);

        foreach ($units as $key => $unit) {
            if (
                \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_ACCEPT == $unit
                || \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_DECLINE == $unit
            ) {
                unset($units[$key]);
            }
        }

        return $units;
    }
}
