<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\XPaymentsConnector\Core;

/**
 * Kount 
 */
class Kount extends \XLite\Base\Singleton
{
    /**
     * Kount data
     */
    protected $kountData = array();

    /**
     * Get Kount data
     *
     * @return object
     */
    public function getKountData(\XLite\Model\Order $order)
    {
        if (!isset($this->kountData[$order->getOrderId()])) {

            $this->kountData[$order->getOrderId()] = false;

            $transactions = $order->getPaymentTransactions();

            foreach ($transactions as $transaction) {

                if (
                    $transaction->getDataCell('xpc_kount')
                    && $transaction->getDataCell('xpc_kount')->getValue()
                ) {
                    $this->kountData[$order->getOrderId()] = unserialize($transaction->getDataCell('xpc_kount')->getValue());

                    break;
                }
            }
        }

        return $this->kountData[$order->getOrderId()];
    }

    /**
     * Get list of Kount errors
     *
     * @return array
     */
    public function getKountErrors(\XLite\Model\Order $order)
    {
        $errors = false;

        if (
            is_object($this->getKountData($order))
            && isset($this->getKountData($order)->errors)
        ) {
            $errors = get_object_vars($this->getKountData($order)->errors);
        }

        return $errors;
    }

    /**
     * Get list of Kount triggered rules
     *
     * @return array
     */
    public function getKountRules(\XLite\Model\Order $order)
    {
        $rules = false;

        if (
            is_object($this->getKountData($order))
            && isset($this->getKountData($order)->rules)
        ) {
            $rules = get_object_vars($this->getKountData($order)->rules);
        }

        return $rules;
    }

    /**
     * Get Kount result as text
     *
     * @return string
     */
    public function getKountMessage(\XLite\Model\Order $order)
    {
        $message = false;

        if (
            is_object($this->getKountData($order))
            && isset($this->getKountData($order)->message)
        ) {
            $message = $this->getKountData($order)->message;
        }

        return $message;
    }

    /**
     * Get Kount transaction ID
     *
     * @return string
     */
    public function getKountTransactionId(\XLite\Model\Order $order)
    {
        $transactionId = false;

        if (
            is_object($this->getKountData($order))
            && isset($this->getKountData($order)->{'Transaction ID'})
        ) {
            $transactionId = $this->getKountData($order)->{'Transaction ID'};
        }

        return $transactionId;
    }

    /**
     * Get Kount score
     *
     * @return string
     */
    public function getKountScore(\XLite\Model\Order $order)
    {
        $score = false;

        if (
            is_object($this->getKountData($order))
            && isset($this->getKountData($order)->Score)
        ) {
            $score = $this->getKountData($order)->Score;
        }

        return $score;
    }

    /**
     * Get CSS class for Kount score
     *
     * @return string
     */
    public function getKountScoreClass(\XLite\Model\Order $order)
    {
        $class = '';

        if (
            is_object($this->getKountData($order))
            && isset($this->getKountData($order)->Auto)
        ) {
            if ($this->getKountData($order)->Auto == 'A') {
                $class = 'success';
            } elseif ($this->getKountData($order)->Auto == 'D') {
                $class = 'danger';
            } else {
                $class = 'warning';
            }
        }
        
        return $class;
    }
}
