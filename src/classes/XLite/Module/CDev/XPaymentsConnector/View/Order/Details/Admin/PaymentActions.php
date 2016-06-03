<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * X-Cart
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the software license agreement
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.x-cart.com/license-agreement.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to licensing@x-cart.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not modify this file if you wish to upgrade X-Cart to newer versions
 * in the future. If you wish to customize X-Cart for your needs please
 * refer to http://www.x-cart.com/ for more information.
 *
 * @category  X-Cart 5
 * @author    Qualiteam software Ltd <info@x-cart.com>
 * @copyright Copyright (c) 2011-2016 Qualiteam software Ltd <info@x-cart.com>. All rights reserved
 * @license   http://www.x-cart.com/license-agreement.html X-Cart 5 License Agreement
 * @link      http://www.x-cart.com/
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
