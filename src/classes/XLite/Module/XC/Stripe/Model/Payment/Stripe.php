<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Stripe\Model\Payment;

/**
 * Stripe payment processor
 */
class Stripe extends \XLite\Model\Payment\Base\Online
{
    /**
     * Stripe library included flag
     *
     * @var boolean
     */
    protected $stripeLibIncluded = false;

    /**
     * Event id 
     * 
     * @var string
     */
    protected $eventId;

    /**
     * Get Webhook URL
     *
     * @return string
     */
    public function getWebhookURL()
    {
        return \XLite::getInstance()->getShopURL(
            \XLite\Core\Converter::buildURL('callback', null, array(), \XLite::getCustomerScript()),
            \XLite\Core\Config::getInstance()->Security->customer_security
        );
    }

    /**
     * Get URL of referral page
     *
     * @param \XLite\Model\Payment\Method $method Payment method
     *
     * @return string
     */
    public function getReferralPageURL(\XLite\Model\Payment\Method $method)
    {
        return '';
    }

    /**
     * Check - payment method is configured or not
     *
     * @param \XLite\Model\Payment\Method $method Payment method
     *
     * @return boolean
     */
    public function isConfigured(\XLite\Model\Payment\Method $method)
    {
        return ($method->getSetting('accessToken') && $method->getSetting('publishKey'))
            || ($method->getSetting('accessTokenTest') && $method->getSetting('publishKeyTest'));
    }

    /**
     * Get allowed backend transactions
     *
     * @return string Status code
     */
    public function getAllowedTransactions()
    {
        return array(
            \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_CAPTURE,            
            \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_CAPTURE_PART,
            \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_VOID,
            \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_REFUND,
            \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_REFUND_PART,
            \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_REFUND_MULTI,
        );
    }

    /**
     * Get settings widget or template
     *
     * @return string Widget class name or template path
     */
    public function getSettingsWidget()
    {
        return '\XLite\Module\XC\Stripe\View\Config';
    }

    /**
     * Get input template
     *
     * @return string|void
     */
    public function getInputTemplate()
    {
        return 'modules/XC/Stripe/payment.twig';
    }

    /**
     * Get input errors
     *
     * @param array $data Input data
     *
     * @return array
     */
    public function getInputErrors(array $data)
    {
        $errors = parent::getInputErrors($data);

        if (empty($data['token'])) {
            $errors[] = \XLite\Core\Translation::lbl(
                'Payment processed with errors. Please, try again or ask administrator'
            );
        }

        return $errors;
    }

    /**
     * Return true if payment method settings form should use default submit button.
     * Otherwise, settings widget must define its own button
     *
     * @return boolean
     */
    public function useDefaultSettingsFormButton()
    {
        return false;
    }

    /**
     * Get initial transaction type (used when customer places order)
     *
     * @param \XLite\Model\Payment\Method $method Payment method object OPTIONAL
     *
     * @return string
     */
    public function getInitialTransactionType($method = null)
    {
        $type = $method ? $method->getSetting('type') : $this->getSetting('type');

        return 'sale' == $type
            ? \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_AUTH
            : \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_SALE;
    }

    /**
     * Get payment method admin zone icon URL
     *
     * @param \XLite\Model\Payment\Method $method Payment method
     *
     * @return string
     */
    public function getAdminIconURL(\XLite\Model\Payment\Method $method)
    {
        return true;
    }

    /**
     * Do initial payment
     *
     * @return string Status code
     */
    protected function doInitialPayment()
    {
        $this->includeStripeLibrary();

        $note = '';

        try {
            $payment = \Stripe_Charge::create(
                array(
                    'amount'      => $this->formatCurrency($this->transaction->getValue()),
                    'currency'    => $this->transaction->getCurrency()->getCode(),
                    'card'        => $this->request['token'],
                    'capture'     => $this->isCapture(),
                    'description' => $this->getInvoiceDescription(),
                )
            );
            $result = static::COMPLETED;
            $backendTransactionStatus = \XLite\Model\Payment\BackendTransaction::STATUS_SUCCESS;

            $type = $this->isCapture()
                ? \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_SALE
                : \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_AUTH;
            $backendTransaction = $this->registerBackendTransaction($type);
            $backendTransaction->setDataCell('stripe_id', $payment->id);
            $this->transaction->setType($type);
            if (!empty($payment->balance_transaction)) {
                $backendTransaction->setDataCell('stripe_b_txntid', $payment->balance_transaction);
            }

            if (!$this->checkResponse($payment, $note)) {
                $result = static::FAILED;
                $backendTransactionStatus = \XLite\Model\Payment\BackendTransaction::STATUS_FAILED;
            }

            $backendTransaction->setStatus($backendTransactionStatus);
            $backendTransaction->registerTransactionInOrderHistory('initial request');

            $this->setDetail('stripe_id', $payment->id);

            if (!empty($payment->card->cvc_check)) {
                $note .= static::t('CVC verification: X', array('state' => $payment->card->cvc_check)) . PHP_EOL;
            }

            if (!empty($payment->card->address_line1_check)) {
                $note .= static::t('Address line verification: X', array('state' => $payment->card->address_line1_check)) . PHP_EOL;
            }

            if (!empty($payment->card->address_zip_check)) {
                $note .= static::t('Address zipcode verification: X', array('state' => $payment->card->address_zip_check)) . PHP_EOL;
            }


        } catch (\Exception $e) {
            $result = static::FAILED;
            \XLite\Core\TopMessage::addError($e->getMessage());
            $note = $e->getMessage();
        }

        $this->transaction->setNote($note);

        return $result;
    }

    /**
     * Format currency 
     * 
     * @param float $value Currency value
     *  
     * @return integer
     */
    protected function formatCurrency($value)
    {
        return $this->transaction->getCurrency()->roundValueAsInteger($value);
    }

    /**
     * Check - transaction is capture type or not
     * 
     * @return boolean
     */
    protected function isCapture()
    {
        return 'sale' == $this->getSetting('type');
    }

    /**
     * Register backend transaction 
     * 
     * @param string                           $type        Backend transaction type OPTIONAL
     * @param \XLite\Model\Payment\Transaction $transaction Transaction OPTIONAL
     *  
     * @return \XLite\Model\Payment\BackendTransaction
     */
    protected function registerBackendTransaction($type = null, \XLite\Model\Payment\Transaction $transaction = null)
    {
        if (!$transaction) {
            $transaction = $this->transaction;
        }

        if (!$type) {
            $type = $transaction->getType();
        }

        $backendTransaction = $transaction->createBackendTransaction($type);

        return $backendTransaction;
    }

    /**
     * Check response 
     * 
     * @param object $payment Charge object
     * @param string &$note   Note
     *  
     * @return boolean
     */
    protected function checkResponse($payment, &$note)
    {
        $result = $this->checkTotal($this->transaction->getCurrency()->convertIntegerToFloat($payment->amount))
            && $this->checkCurrency(strtoupper($payment->currency));

        if ($result && $payment->captured != $this->isCapture()) {
            $result = false;
            $note .= static::t(
                'Requested transaction type: X; real transaction type: Y',
                array(
                    'actual' => $this->isCapture() ? 'capture' : 'authorization',
                    'real'   => $payment->captured ? 'capture' : 'authorization',
                )
            );
        }

        return $result;
    }

    /**
     * Include Stripe library
     *
     * @return void
     */
    protected function includeStripeLibrary()
    {
        if (!$this->stripeLibIncluded) {
            require_once LC_DIR_MODULES . 'XC' . LC_DS . 'Stripe' . LC_DS . 'lib' . LC_DS . 'Stripe.php';

            if ($this->transaction) {
                $method = $this->transaction->getPaymentMethod();
                $suffix = $this->isTestMode($method) ? 'Test' : '';
                $key = $method->getSetting('accessToken' . $suffix);

            } else {
                $method = \XLite\Core\Database::getRepo('XLite\Model\Payment\Method')
                    ->findOneBy(array('service_name' => 'Stripe'));
                $suffix = $this->isTestMode($method) ? 'Test' : '';
                $key = $method->getSetting('accessToken' . $suffix);
            }

            \Stripe::setApiKey($key);

            $this->stripeLibIncluded = true;
        }
    }

    // {{{ Secondary transactions

    /**
     * Capture
     *
     * @param \XLite\Model\Payment\BackendTransaction $transaction Backend transaction
     *
     * @return boolean
     */
    protected function doCapture(\XLite\Model\Payment\BackendTransaction $transaction)
    {
        $this->includeStripeLibrary();

        $backendTransactionStatus = \XLite\Model\Payment\BackendTransaction::STATUS_FAILED;

        try {
            $payment = \Stripe_Charge::retrieve(
                $transaction->getPaymentTransaction()->getDataCell('stripe_id')->getValue()
            );
            $payment->capture();

            if ($payment->captured) {
                $backendTransactionStatus = \XLite\Model\Payment\BackendTransaction::STATUS_SUCCESS;
            }

            if (!empty($payment->balance_transaction)) {
                $transaction->setDataCell('stripe_b_txntid', $payment->balance_transaction);
            }

        } catch (\Exception $e) {
            $transaction->setDataCell('errorMessage', $e->getMessage());
            \XLite\Logger::getInstance()->log($e->getMessage(), LOG_ERR);
            \XLite\Core\TopMessage::addError($e->getMessage());
        }

        $transaction->setStatus($backendTransactionStatus);
         
        return \XLite\Model\Payment\BackendTransaction::STATUS_SUCCESS == $backendTransactionStatus;
    }

    /**
     * Void
     *
     * @param \XLite\Model\Payment\BackendTransaction $transaction Backend transaction
     *
     * @return boolean
     */
    protected function doVoid(\XLite\Model\Payment\BackendTransaction $transaction)
    {
        return $this->doRefund($transaction, true);
    }

    /**
     * Refund
     *
     * @param \XLite\Model\Payment\BackendTransaction $transaction Backend transaction
     * @param boolean                                 $isDoVoid    Is void action OPTIONAL
     *
     * @return boolean
     */
    protected function doRefund(\XLite\Model\Payment\BackendTransaction $transaction, $isDoVoid = false)
    {
        $this->includeStripeLibrary();

        $backendTransactionStatus = \XLite\Model\Payment\BackendTransaction::STATUS_FAILED;

        try {
            $payment = \Stripe_Charge::retrieve(
                $transaction->getPaymentTransaction()->getDataCell('stripe_id')->getValue()
            );
            if ($transaction->getValue() != $transaction->getPaymentTransaction()->getValue()) {
                $payment->refund(
                    array(
                        'id'     => $transaction->getPaymentTransaction()->getDataCell('stripe_id')->getValue(),
                        'amount' => $this->formatCurrency($transaction->getValue()),
                    )
                );
                $refundTransaction = null;
                foreach ($payment->refunds as $r) {
                    if (!$this->isRefundTransactionRegistered($r)) {
                        $refundTransaction = $r;
                        break;
                    }
                }

            } else {
                $payment->refund();
                $refundTransaction = reset($payment->refunds);
            }

            if ($refundTransaction) {
                $backendTransactionStatus = \XLite\Model\Payment\BackendTransaction::STATUS_SUCCESS;

                $transaction->setDataCell('stripe_date', $refundTransaction->created);
                if ($refundTransaction->balance_transaction) {
                    $transaction->setDataCell('stripe_b_txntid', $refundTransaction->balance_transaction);
                }
            }

        } catch (\Exception $e) {
            $transaction->setDataCell('errorMessage', $e->getMessage());
            \XLite\Logger::getInstance()->log($e->getMessage(), LOG_ERR);
            \XLite\Core\TopMessage::addError($e->getMessage());
        }

        $transaction->setStatus($backendTransactionStatus);

        return \XLite\Model\Payment\BackendTransaction::STATUS_SUCCESS == $backendTransactionStatus;
    }

    /**
     * Check - specified rfund transaction is registered or not
     * 
     * @param object $refund Refund transaction
     *  
     * @return boolean
     */
    protected function isRefundTransactionRegistered($refund)
    {
        $result = null;
        $types = array(
            \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_REFUND,
            \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_REFUND_PART,
            \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_REFUND_MULTI,
        );

        foreach ($this->transaction->getBackendTransactions() as $bt) {
            $txnid = $bt->getDataCell('stripe_b_txntid');
            if (
                in_array($bt->getType(), $types)
                && (!$txnid || $txnid->getValue() == $refund->balance_transaction)
                && ($bt->getDataCell('stripe_date') && $bt->getDataCell('stripe_date')->getValue() == $refund->created)
            ) {
                $result = $bt;
                break;
            }
        }

        return $result;
    }

    // }}}

    // {{{ Callback

    /**
     * Get callback wner transaction 
     * 
     * @return \XLite\Model\Payment\Transaction
     */
    public function getCallbackOwnerTransaction()
    {
        $transaction = null;

        $eventId = $this->detectEventId();
        if ($eventId) {
            $this->includeStripeLibrary();

            try {
                $event = \Stripe_Event::retrieve($eventId);
                if ($event) {
                    $transaction = \XLite\Core\Database::getRepo('XLite\Model\Payment\Transaction')
                        ->findOneByCell('stripe_id', $event->data->object->id);
                    if ($transaction) {
                        $this->eventId = $eventId;
                    }
                }

            } catch (\Exception $e) {
            }
        }

        return $transaction;
    }

    /**
     * Process callback
     *
     * @param \XLite\Model\Payment\Transaction $transaction Callback-owner transaction
     *
     * @return void
     */
    public function processCallback(\XLite\Model\Payment\Transaction $transaction)
    {
        parent::processCallback($transaction);

        $this->includeStripeLibrary();

        try {
            $event = \Stripe_Event::retrieve($this->eventId);
            if ($event) {
                $name = 'processEvent' . \XLite\Core\Converter::convertToCamelCase(str_replace('.', '_', $event->type));
                if (method_exists($this, $name)) {
                    // $name assembled from 'processEvent' + event type
                    $this->$name($event);
                    \XLite\Core\Database::getEM()->flush();
                }

                \XLite\Logger::getInstance()->logCustom(
                    'stripe',
                    'Event handled: ' . $event->type . ' # ' . $this->eventId . PHP_EOL
                    . 'Processed: ' . (method_exists($this, $name) ? 'Yes' : 'No')
                );
            }

        } catch (\Exception $e) {
        }
    }

    /**
     * Process event charge.refunded
     *
     * @param \Stripe_Event $event Event
     *
     * @return void
     */
    protected function processEventChargeRefunded($event)
    {
        $founded = null;

        foreach ($this->transaction->getBackendTransactions() as $bt) {
            if (
                $bt->getType() == \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_REFUND
                && $bt->getStatus() == \XLite\Model\Payment\BackendTransaction::STATUS_SUCCESS
            ) {
                $founded = $bt;
                break;
            }
        }

        $refundTransaction = null;
        foreach ($event->data->object->refunds as $r) {
            if (!$this->isRefundTransactionRegistered($r)) {
                $refundTransaction = $r;
                break;
            }
        }

        if ($refundTransaction && !$founded) {
            $amount = $this->transaction->getCurrency()->convertIntegerToFloat($refundTransaction->amount);

            if ($amount != $this->transaction->getValue()) {
                $backendTransaction = $this->registerBackendTransaction(
                    \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_REFUND_PART
                );
                $backendTransaction->setValue($amount);

            } else {
                $type = \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_REFUND;
                if (!$this->transaction->isCaptured()) {
                    $type = \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_VOID;
                    $this->transaction->setType($type);
                    $this->transaction->setStatus(\XLite\Model\Payment\Transaction::STATUS_VOID);
                }
                $backendTransaction = $this->registerBackendTransaction($type);
            }

            $backendTransaction->setDataCell('stripe_date', $refundTransaction->created);
            if ($refundTransaction->balance_transaction) {
                $backendTransaction->setDataCell('stripe_b_txntid', $refundTransaction->balance_transaction);
            }

            $backendTransaction->setStatus(\XLite\Model\Payment\BackendTransaction::STATUS_SUCCESS);
            $backendTransaction->registerTransactionInOrderHistory('callback');

        } elseif ($founded) {
            $this->transaction->setType(\XLite\Model\Payment\BackendTransaction::TRAN_TYPE_REFUND);
            $this->transaction->setStatus(\XLite\Model\Payment\Transaction::STATUS_SUCCESS);
        } else{
            \XLite\Logger::getInstance()->logCustom(
                'stripe',
                'Duplicate charge.refunded event # ' . $event->id
            );
        }
    }

    /**
     * Process event charge.captured 
     * 
     * @param \Stripe_Event $event Event
     *  
     * @return void
     */
    protected function processEventChargeCaptured($event)
    {
        $founded = null;

        foreach ($this->transaction->getBackendTransactions() as $bt) {
            if (
                $bt->getType() == \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_CAPTURE
                && $bt->getStatus() == \XLite\Model\Payment\BackendTransaction::STATUS_SUCCESS
            ) {
                $founded = $bt;
                break;
            }
        }

        $refundTransaction = null;
        foreach ($event->data->object->refunds as $r) {
            if (!$this->isRefundTransactionRegistered($r)) {
                $refundTransaction = $r;
                break;
            }
        }

        if (!$founded) {
            $amount = $this->transaction->getValue();
            if ($refundTransaction) {
                $amountRefunded = $this->transaction->getCurrency()->convertIntegerToFloat($refundTransaction->amount);
                $amountFull = $this->transaction->getCurrency()->convertIntegerToFloat($event->data->object->amount);
                $amount = $amountFull - $amountRefunded;
                if ($amount != $this->transaction->getValue()) {
                    $backendTransaction = $this->registerBackendTransaction(
                        \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_REFUND_MULTI
                    );
                    $backendTransaction->setValue($amountRefunded);
                    $backendTransaction->setStatus(\XLite\Model\Payment\BackendTransaction::STATUS_SUCCESS);
                    $backendTransaction->registerTransactionInOrderHistory('callback');
                }                
            }

            $type = \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_CAPTURE;
            if ($refundTransaction) {
                $type = \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_CAPTURE_PART;
                $backendTransaction = $this->registerBackendTransaction($type);
                $backendTransaction->setValue($amount);

                $this->transaction->setType(\XLite\Model\Payment\BackendTransaction::TRAN_TYPE_CAPTURE_PART);                    
                $this->transaction->setValue($amount);
                $this->transaction->setStatus(\XLite\Model\Payment\Transaction::STATUS_SUCCESS);

            }else{                
                $backendTransaction = $this->registerBackendTransaction($type);
            }
            $backendTransaction->setStatus(\XLite\Model\Payment\BackendTransaction::STATUS_SUCCESS);
            $backendTransaction->registerTransactionInOrderHistory('callback');

        } else {
            \XLite\Logger::getInstance()->logCustom(
                'stripe',
                'Duplicate charge.captured event # ' . $event->id
            );
        }
    }


    /**
     * Detect event id 
     * 
     * @return string
     */
    protected function detectEventId()
    {
        $body = @file_get_contents('php://input');
        $event = @json_decode($body);
        $id = $event ? $event->id : null;

        return ($id && preg_match('/^evt_/Ss', $id)) ? $id : null;
    }

    // }}}

    // {{{ Service requests

    /**
     * Retrieve acount 
     * 
     * @return \Stripe_Account
     */
    public function retrieveAcount()
    {
        $this->includeStripeLibrary();

        try {
            $account = \Stripe_Account::retrieve();

        } catch (\Exception $e) {
            $account = null;
        }

        return $account;
    }

    // }}}

}

