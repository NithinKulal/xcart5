<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\XPaymentsConnector\Model;

/**
 * XPayments payment processor
 *
 */
class Order extends \XLite\Model\Order implements \XLite\Base\IDecorator
{
    /** 
     * Fraud statuses
     */
    const FRAUD_STATUS_CLEAN    = 'Clean';
    const FRAUD_STATUS_FRAUD    = 'Fraud';
    const FRAUD_STATUS_REVIEW   = 'Review';
    const FRAUD_STATUS_UNKNOWN  = '';

    /**
     * Fraud types
     */
    const FRAUD_TYPE_KOUNT = 'kount';
    const FRAUD_TYPE_GATEWAY = 'gateway'; 

    /**
     * Order fraud status
     *
     * @var string
     *
     * @Column (type="string")
     */
    protected $fraud_status_xpc = '';

    /**
     * Order fraud type (which system considered the transaction fraudulent)
     *
     * @var string
     *
     * @Column (type="string")
     */
    protected $fraud_type_xpc = '';

    /**
     * Get visible payment methods
     *
     * @return array
     */
    public function getCCData()
    {
        $result = array();

        foreach ($this->getPaymentTransactions() as $transaction) {

            if ($transaction->getCard()) {
                $result[] = $transaction->getCard();
            }

        }

        return $result;
    }

    /**
     * Get items for card setup there should be only one item
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getItems()
    {
        $items = parent::getItems();

        foreach ($items as $key => $item) {
            if ($item->isXpcFakeItem()) {
                $items->clear();
                $items[] = $item;
                break;
            }
        }

        return $items;
    }

    /**
     * Get total
     *
     * @return decimal
     */
    public function getTotal()
    {
        if (
            $this->getItems()
            && $this->getItems()->last()
            && $this->getItems()->last()->isXpcFakeItem()
        ) {
            $total = $this->getItems()->last()->getPrice();
        } else {
            $total = parent::getTotal();
        }
        
        return $total;
    }

    /**
     * Get transaction xpc_ values for the entire order. What was actually authorized, captured, voided, and refunded.
     *
     * @return array
     */
    protected function getXpcTransactionSums()
    {
        $orderAuthorized = 0;
        $orderCaptured = 0;
        $orderVoided = 0;
        $orderRefunded = 0;

        $xpcFound = false;

        $transactions = $this->getPaymentTransactions();

        foreach ($transactions as $t) {

            if ($t->isXpc(true)) {

                $xpcFound = true;

                list($authorized, $captured, $voided, $refunded) = $t->getXpcValues(); 

                $orderAuthorized += $authorized;
                $orderCaptured   += $captured;
                $orderVoided     += $voided;
                $orderRefunded   += $refunded;
            }
        }

        return array($orderAuthorized, $orderCaptured, $orderVoided, $orderRefunded, $xpcFound);
    }
    
    /**
     * Set order status by transaction
     *
     * @param \XLite\Model\Payment\transaction $transaction Transaction which changes status
     *
     * @return void
     */
    public function setPaymentStatusByTransaction(\XLite\Model\Payment\transaction $transaction)
    {
        if ($transaction->isXpc(true)) {

            $config = \XLite\Core\Config::getInstance()->CDev->XPaymentsConnector;
        
            list($authorized, $captured, $voided, $refunded) = $this->getXpcTransactionSums();

            if (
                $transaction->getDataCell('xpc_is_fraud_status')
                && 1 == $transaction->getDataCell('xpc_is_fraud_status')->getValue()
                && self::ORDER_ZERO >= $authorized
                && self::ORDER_ZERO >= $captured
                && self::ORDER_ZERO >= $voided
                && self::ORDER_ZERO >= $refunded
                && $transaction->getValue() > self::ORDER_ZERO
            ) {

                $status = $config->xpc_status_declined;

            } elseif ($refunded > 0) {

                if ($refunded >= $captured) {
                    $status = $config->xpc_status_refunded; 
                } else {
                    $status = $config->xpc_status_refunded_part;
                }

            } elseif ($voided > 0) {

                $status = $config->xpc_status_declined;

            } elseif ($captured > 0) {

                if ($captured >= $authorized) {
                    $status = $config->xpc_status_charged;
                } else {
                    $status = $config->xpc_status_charged_part;
                }

            } elseif (
                $authorized > 0
                || (
                    $authorized <= self::ORDER_ZERO
                    && $this->getTotal() <= self::ORDER_ZERO
                )
            ) {

                $status = $config->xpc_status_auth;

            } else {

                $status = $config->xpc_status_new;
            } 

            $this->setPaymentStatus($status);

        } else {
            parent::setPaymentStatusByTransaction($transaction);
        }

    }

    /**
     * Get array of payment transaction sums (how much is authorized, captured and refunded)
     *
     * @return array
     */
    public function getPaymentTransactionSums()
    {
        static $paymentTransactionSums = null;

        if (!isset($paymentTransactionSums)) {

            list($authorized, $captured, $voided, $refunded, $xpcFound) = $this->getXpcTransactionSums();

            if (!$xpcFound) {

                $paymentTransactionSums = parent::getPaymentTransactionSums();

            } else {

                if ($captured > self::ORDER_ZERO) {
                    // Substract the captured amount from the authorized.
                    // This makes only actual authorized amount being displayed.
                    $authorized = max($authorized - $captured, 0);
                }

                if ($voided > self::ORDER_ZERO) {
                    // Substract the declined amount from the authorized.
                    // This makes only actual authorized amount being displayed,
                    // and we hide the declined amount.
                    $authorized = max($authorized - $voided, 0);
                    $voided = 0;
                }
           
                $paymentTransactionSums = array(
                    static::t('Authorized amount') => $authorized,
                    static::t('Charged amount')    => $captured,
                    static::t('Refunded amount')   => $refunded,
                    static::t('Declined amount')   => $voided, // This is zero anyway, but let it stay just in case.
                );
            }

            if (
                $xpcFound
                || $this->hasSavedCardsInProfile()
            ) {

                $paymentTransactionSums[static::t('Difference')] = $this->getAomTotalDifference();

                // Remove from array all zero sums
                foreach ($paymentTransactionSums as $k => $v) {
                    if ($v <= self::ORDER_ZERO) {
                        unset($paymentTransactionSums[$k]);
                    }
                }
            }
        }

        return $paymentTransactionSums;
    }

    /**
     * Difference in order Total after AOM changes if (any)
     *
     * @return float
     */
    public function getAomTotalDifference()
    {
        // Apparently we'll need to change this
        return $this->getOpenTotal();
    }

    /**
     * Check if total difference after AOM changes is greater than zero
     *
     * @return boolean
     */
    public function isAomTotalDifferencePositive()
    {
        return $this->getAomTotalDifference() > \XLite\Model\Order::ORDER_ZERO;
    }

    /**
     * Does profile has saved cards 
     *
     * @return boolean
     */
    protected function hasSavedCardsInProfile()
    {
        return $this->getProfile()
            && $this->getProfile()->getSavedCards();
    }

    /**
     * Is recharge allowed for the order 
     *
     * @return boolean
     */
    public function isAllowRecharge()
    {
        return $this->isAomTotalDifferencePositive()
            && $this->hasSavedCardsInProfile(); 
    }

    /**
     * Return anchor name for the information about fraud check on the order details page
     *
     * @return string 
     */
    public function getFraudInfoXpcAnchor()
    {
        $anchor = '';

        if (self::FRAUD_TYPE_KOUNT == $this->getFraudTypeXpc()) {
            $anchor = 'fraud-info-kount';
        } elseif (self::FRAUD_TYPE_GATEWAY == $this->getFraudTypeXpc()) {
            $anchor = 'fraud-info-gateway';
        }

        return $anchor;
    }


    /**
     * Set fraud_status_xpc
     *
     * @param string $fraudStatusXpc
     * @return Order
     */
    public function setFraudStatusXpc($fraudStatusXpc)
    {
        $this->fraud_status_xpc = $fraudStatusXpc;
        return $this;
    }

    /**
     * Get fraud_status_xpc
     *
     * @return string 
     */
    public function getFraudStatusXpc()
    {
        return $this->fraud_status_xpc;
    }

    /**
     * Set fraud_type_xpc
     *
     * @param string $fraudTypeXpc
     * @return Order
     */
    public function setFraudTypeXpc($fraudTypeXpc)
    {
        $this->fraud_type_xpc = $fraudTypeXpc;
        return $this;
    }

    /**
     * Get fraud_type_xpc
     *
     * @return string 
     */
    public function getFraudTypeXpc()
    {
        return $this->fraud_type_xpc;
    }
}
