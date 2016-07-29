<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\XPaymentsConnector\Model\Payment\Processor;

/**
 * XPayments payment processor
 */
class XPayments extends \XLite\Module\CDev\XPaymentsConnector\Model\Payment\Processor\AXPayments
{
    /**
     * Form fields
     *
     * @var array
     */
    protected $formFields;

    /**
     * Get input template
     *
     * @return string|void
     */
    public function getInputTemplate()
    {
        return 'modules/CDev/XPaymentsConnector/checkout/save_card_box.twig';
    }

    /**
     * Returns the list of settings available for this payment processor
     *
     * @return array
     */
    public function getAvailableSettings()
    {
        return array(
            'name',
            'id',
            'sale',
            'auth',
            'capture',
            'capturePart',
            'captureMulti',
            'void',
            'voidPart',
            'voidMulti',
            'refund',
            'refundPart',
            'refundMulti',
            'getInfo',
            'accept',
            'decline',
            'test',
            'authExp',
            'captMinLimit',
            'captMaxLimit',
            'moduleName',
            'settingsHash',
            'saveCards',
            'canSaveCards',
            'currency',
            'isTestMode',
        );
    }

    /**
     * Process return
     *
     * @param \XLite\Model\Payment\Transaction $transaction Return-owner transaction
     *
     * @return void
     */
    public function processReturn(\XLite\Model\Payment\Transaction $transaction)
    {
        $txnId = \XLite\Core\Request::getInstance()->txnId;
        $info = $this->client->requestPaymentInfo($txnId);

        $transactionStatus = $transaction::STATUS_FAILED;

        $this->client->clearInitDataFromSession();

        if ($info->isSuccess()) {

            $response = $info->getResponse();

            $transaction->setDataCell('xpc_message', $response['message'], 'X-Payments response');

            if ($response['isFraudStatus']) {
                $transaction->setDataCell('xpc_fmf', 'blocked', 'Fraud status');
            }

            $errorDescription = false;

            if (abs($response['amount'] - $transaction->getValue()) > 0.01) {

                // Total wrong
                $errorDescription = 'Total amount doesn\'t match. '
                    . 'Transaction total: ' . $transaction->getValue() . ', '
                    . 'X-Payments amount: ' . $response['amount'];
            }

            if (!$transaction->getCurrency()) {

                // Adjust currency if it's not set
                $currency = \XLite::getInstance()->getCurrency();
                $transaction->setCurrency($currency);
            }

            if ($response['currency'] != $transaction->getCurrency()->getCode()) {

                // Currency wrong
                $errorDescription = 'Currency doesn\'t match. '
                    . 'Transaction currency: ' . $transaction->getCurrency()->getCode() . ', '
                    . 'X-Payments currency: ' . $response['currency'];
            }

            if ($errorDescription) {

                // Set error details, status remails Failed
                $transaction->setDataCell('error', 'Hacking attempt!', 'Error');
                $transaction->setDataCell('errorDescription', $errorDescription, 'Hacking attempt details');

            } else {

                // Set the transaction status
                $transactionStatus = $this->getTransactionStatus($response, $transaction);

                if (
                    !empty($response['saveCard'])
                    && version_compare(\XLite\Core\Config::getInstance()->CDev->XPaymentsConnector->xpc_api_version, '1.6') >= 0
                ) {

                    if ($transaction->getXpcData()) {
                        $transaction->getXpcData()->setUseForRecharges(($response['saveCard'] == 'Y') ? 'Y' : 'N');
                    }

                }
            }
        }

        if ($transactionStatus) {
            $transaction->setStatus($transactionStatus);
        }

        // AntiFraud check block by AVS
        if (
            method_exists($transaction, 'checkAvsDataValid')
            && $transaction->checkAvsDataValid()
        ) {

            $result = $transaction->getAntiFraudResult();

            if ($transaction->checkBlockAvs()) {
                $result->setDataCell('blocked_by_avs', '1');
                $result->setScore($result::MAX_SCORE);
            } else {
                $result->setDataCell('blocked_by_avs', '0');
            }
        }


        $this->transaction = $transaction;
    }

    /**
     * This is not Saved Card payment method
     *
     * @return boolean
     */
    protected function isSavedCardsPaymentMethod()
    {
        return false;
    }

    /**
     * Do initial payment
     *
     * @return string Status code
     */
    protected function doInitialPayment()
    {
        $status = parent::doInitialPayment();
        if (
            static::PROLONGATION == $status
            && \XLite\Module\CDev\XPaymentsConnector\Core\Iframe::getInstance()->useIframe()
        ) {
            exit ();
        }

        return $status;
    }

    /**
     * Get redirect form URL
     *
     * @return string
     */
    protected function getFormURL()
    {
        $config = \XLite\Core\Config::getInstance()->CDev->XPaymentsConnector;

        return preg_replace('/\/+$/Ss', '', $config->xpc_xpayments_url) . '/payment.php';
    }

    /**
     * Get redirect form fields list
     *
     * @return array
     */
    protected function getFormFields()
    {
        $this->formFields = $this->client->getFormFields($this->transaction);

        return $this->formFields;
    }

    /**
     * Save some payment settings from payment method to the transaction
     *
     * @param \XLite\Model\Payment\Transaction $transaction Transaction
     *
     * @return string
     */
    public function savePaymentSettingsToTransaction(\XLite\Model\Payment\Transaction $transaction, $parentTransaction = null)
    {
        if ($parentTransaction) {

            $paymentMethod = $parentTransaction->getPaymentMethod();

        } else {

            $paymentMethod = $transaction->getPaymentMethod();
        }

        foreach ($this->paymentSettingsToSave as $field) {

            $key = 'xpc_can_do_' . $field;
            if ($paymentMethod->getSetting($field)) {
                $transaction->setDataCell($key, $paymentMethod->getSetting($field), null, 'C');
            }
        }

        $transaction->setDataCell('xpc_session_id', \XLite\Core\Session::getInstance()->getID(), null, 'C');
    }

    /**
     * Pay
     *
     * @param \XLite\Model\Payment\Transaction $transaction Transaction
     * @param array                            $request     Input data request OPTIONAL
     *
     * @return string
     */
    public function pay(\XLite\Model\Payment\Transaction $transaction, array $request = array())
    {
        $this->savePaymentSettingsToTransaction($transaction);

        return parent::pay($transaction, $request);
    }

    /**
     * Check - payment method has enabled test mode or not
     *
     * @param \XLite\Model\Payment\Method $method Payment method
     *
     * @return boolean
     */
    public function isTestMode(\XLite\Model\Payment\Method $method)
    {
        return 'Y' == $method->getSetting('isTestMode');
    }
}
