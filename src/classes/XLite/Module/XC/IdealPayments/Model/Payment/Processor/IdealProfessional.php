<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\IdealPayments\Model\Payment\Processor;

/**
 * iDEAL Professional (Rabobank) integration (v.3.3.1)
 *
 * Find the latest API document here:
 * https://www.rabobank.nl/bedrijven/producten/betalen_en_ontvangen/alle_producten/ideal/
 * (this one was used during development: https://www.rabobank.nl/images/manual_ideal_professional_29545793.pdf)
 */
class IdealProfessional extends \XLite\Model\Payment\Base\WebBased
{
    /**
     * Get operation types
     *
     * @return array
     */
    public function getOperationTypes()
    {
        return array(
            self::OPERATION_SALE,
        );
    }

    /**
     * Get settings widget or template
     *
     * @return string Widget class name or template path
     */
    public function getSettingsWidget()
    {
        return 'modules/XC/IdealPayments/config.twig';
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
        parent::processReturn($transaction);

        \XLite\Module\XC\IdealPayments\Main::addLog(
            'processReturn',
            \XLite\Core\Request::getInstance()->getData()
        );

        $message = '';

        $data = array();

        if (\XLite\Core\Request::getInstance()->ec && \XLite\Core\Request::getInstance()->trxid) {

            // iDEAL must redirect customer to URL with parameters ec and trxid

            $status = $transaction::STATUS_FAILED;

            // Perform request for transaction status to iDEAL server
            $statusRequest = $this->getIdealRequest('status');
            $statusRequest->setTransactionId(\XLite\Core\Request::getInstance()->trxid);

            $transactionStatus = $statusRequest->doRequest();

            if ($statusRequest->hasErrors()) {

                // Response contains errors

                foreach($statusRequest->getErrors() as $errorEntry) {
                    $message .= $errorEntry['desc'] . PHP_EOL;
                }

            } elseif (!empty($transactionStatus)) {

                // Transaction status successfully received

                if ($statusRequest->getAccountName()) {
                    $data['accountName'] = utf8_encode($statusRequest->getAccountName());
                }

                if ($statusRequest->getAccountNumber()) {
                    $data['accountNum']  = utf8_encode($statusRequest->getAccountNumber());
                }

                if (strcmp($transactionStatus, 'SUCCESS') === 0) {
                    // Transaction is successful
                    $status = $transaction::STATUS_SUCCESS;

                } elseif (strcmp($transactionStatus, 'CANCELLED') === 0) {

                    // Transaction is canceled by customer

                    $this->setDetail(
                        'status',
                        'Customer has canceled checkout before completing their payments',
                        'Status'
                    );

                    $this->transaction->setNote('Customer has canceled checkout before completing their payments');
                    $status = $transaction::STATUS_CANCELED;

                } else {

                    // Transaction is failed by iDEAL payment gateway

                    foreach($statusRequest->getErrors() as $errorEntry) {
                        $message .= $errorEntry['desc'] . PHP_EOL;
                    }
                }

            } else {
                $message = static::t('Unexpected result was received from iDEAL (transaction status is not set)');
            }

        } else {
            $message = static::t('Payment return page requested without expected parameters');
        }

        // Save data in order history

        if ($message) {
            $data['message'] = $message;
        }

        if (!empty($data)) {
            $this->saveFilteredData($data);
        }

        // Set transaction status
        $this->transaction->setStatus($status);
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
        return \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_SALE;
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
        return parent::isConfigured($method)
            && $this->isOpenSSLExists()
            && $this->isAllSettingsProvided($method)
            && $this->checkProcessorSettings();
    }

    /**
     * Return true if openSSL is available
     *
     * @return boolean
     */
    public function isOpenSSLExists()
    {
        return function_exists('openssl_x509_read')
            && function_exists('openssl_x509_export')
            && function_exists('openssl_get_privatekey')
            && function_exists('openssl_sign')
            && function_exists('openssl_free_key');
    }

    /**
     * Check - payment method is configured or not
     *
     * @param \XLite\Model\Payment\Method $method Payment method
     *
     * @return boolean
     */
    public function isAllSettingsProvided(\XLite\Model\Payment\Method $method)
    {
        return $method->getSetting('merchant_id')
            && $method->getSetting('pub_cert')
            && $method->getSetting('pub_key')
            && $method->getSetting('private_key')
            && $method->getSetting('private_key_pass')
            && $method->getSetting('currency');
    }


    /**
     * Check processor settings and return true if they are valid
     *
     * @return boolean
     */
    public function checkProcessorSettings()
    {
        $result = true;

        try {
            $testRequest = $this->getIdealRequest('issuer');
            $testRequest->checkSignature();

        } catch (\Exception $e) {
            $result = false;
        }

        return $result;
    }

    /**
     * Get return type
     *
     * @return string
     */
    public function getReturnType()
    {
        return self::RETURN_TYPE_HTTP_REDIRECT;
    }

    /**
     * Returns the list of settings available for this payment processor
     *
     * @return array
     */
    public function getAvailableSettings()
    {
        return array(
            'merchant_id',
            'subid',
            'pub_cert',
            'pub_key',
            'private_key',
            'private_key_pass',
            'currency',
            'prefix',
            'test',
            'debug_enabled',
        );
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
     * Check - payment method has enabled test mode or not
     *
     * @param \XLite\Model\Payment\Method $method Payment method
     *
     * @return boolean
     */
    public function isTestMode(\XLite\Model\Payment\Method $method)
    {
        return 'Y' == $this->getSetting('test');
    }

    /**
     * Start payment transaction
     *
     * @param string  $issuerId Issuer ID selected by customer
     * @param integer $transid  Current transaction ID
     *
     * @return void
     */
    public function doTransactionRequest($issuerId, $transid)
    {
        if ($issuerId) {

            if (!$this->transaction && $transid) {
                $this->transaction = \XLite\Core\Database::getRepo('XLite\Model\Payment\Transaction')
                    ->findOneBy(array('public_id' => $transid));
            }

            if ($this->transaction) {

                $transRequest = $this->getIdealRequest('transaction');

                $orderId = $this->getTransactionId();

                $transRequest->setIssuerId($issuerId);
                $transRequest->setOrderId($orderId);
                $transRequest->setOrderAmount(round($this->transaction->getValue(), 2));
                $transRequest->setOrderDescription('Order #' . $this->getTransactionId());
                $transRequest->setEntranceCode($this->transaction->getPublicId());

                $transactionID = $transRequest->doRequest();

                if ($transRequest->hasErrors()) {

                    foreach($transRequest->getErrors() as $errorEntry) {
                        \XLite\Core\TopMessage::addError($errorEntry['desc']);
                    }

                } else {
                    $transRequest->doTransaction();
                }

            } else {
                \XLite\Core\TopMessage::addError('Unknown payment transaction');
            }
        }
    }

    /**
     * Get list of issuers from iDEAL
     *
     * @return array
     */
    public function doIssuerRequest()
    {
        $issuerRequest = $this->getIdealRequest('issuer');

        $issuers = $issuerRequest->doRequest();

        if ($issuerRequest->hasErrors()) {
            foreach($issuerRequest->getErrors() as $errorEntry) {
                \XLite\Core\TopMessage::addError($errorEntry['desc']);
            }
        }

        return $issuers;
    }

    /**
     * Generate transaction ID
     *
     * @param \XLite\Model\Payment\Transaction $transaction Transaction
     * @param string                           $prefix      Prefix OPTIONAL
     *
     * @return string
     */
    public function generateTransactionId(\XLite\Model\Payment\Transaction $transaction, $prefix = null)
    {
        return substr(preg_replace('/[^[:alnum:]]/', '', parent::generateTransactionId($transaction, $prefix)), 0, 40);
    }

    /**
     * Get iDEAL request object
     *
     * @param string $requestType Type of request (transaction, status or issuer)
     *
     * @return \IdealProRequest
     */
    protected function getIdealRequest($requestType)
    {
        $params = $this->getIdealPaymentSettings();

        if ('transaction' == $requestType) {
            $params['returnURL'] = $this->getReturnURL(null, true);
        }

        require_once \XLite\Module\XC\IdealPayments\Main::getLibClassesFile();

        $className = '\IdealPro' . ucfirst($requestType) . 'Request';

        return new $className($params);
    }

    /**
     * Get array of payment settings
     *
     * @return array
     */
    protected function getIdealPaymentSettings()
    {
        $result = array();

        $fields = $this->getAvailableSettings();

        foreach ($fields as $field) {
            $result[$field] = $this->getSetting($field);
        }

        if (empty($result['subid'])) {
            // SubID is optional parameter. If not specified we must set up this to the default value '0'
            $result['subid'] = '0';
        }

        $result['securePath'] = LC_DIR_MODULES . 'XC' . LC_DS . 'IdealPayments' . LC_DS . 'certs' . LC_DS;
        $result['cachePath'] = LC_DIR_DATACACHE . LC_DS . 'XC' . LC_DS . 'IdealPayments' . LC_DS;

        return $result;
    }

    /**
     * Get certificates path
     *
     * @return string
     */
    public function getCertificatesPath()
    {
        return 'classes/XLite/Module/XC/IdealPayments/certs';
    }

    /**
     * Get payment method setting by name
     *
     * @param string $name Setting name
     *
     * @result string
     */
    protected function getSetting($name)
    {
        $result = parent::getSetting($name);

        if (is_null($result)) {
            $method = \XLite\Core\Database::getRepo('XLite\Model\Payment\Method')->findOneBy(array('service_name' => 'iDEAL Professional'));
            $result = $method
                ? $method->getSetting($name)
                : null;
        }

        return $result;
    }

    /**
     * Get redirect form URL
     *
     * @return string
     */
    protected function getFormURL()
    {
        return \XLite\Core\Converter::buildURL('ideal_pro', 'transaction');
    }

    /**
     * Get redirect form fields list
     *
     * @return array
     */
    protected function getFormFields()
    {
        $data = \XLite\Core\Request::getInstance()->getData();

        return array(
            'iid'       => $data['payment']['iid'],
            'transid'   => $this->getTransactionId(),
            'returnURL' => $this->getReturnURL(null, true),
        );
    }

    /**
     * Define saved into transaction data schema
     *
     * @return array
     */
    protected function defineSavedData()
    {
        $data = parent::defineSavedData();

        $data['message'] = 'Message';
        $data['accountNum'] = 'Account number';
        $data['accountName'] = 'Account name';

        return $data;
    }

    // {{{ Checkout

    /**
     * Get input template
     *
     * @return string
     */
    public function getInputTemplate()
    {
        return 'modules/XC/IdealPayments/checkout/ideal_professional.twig';
    }

    /**
     * Process input errors
     *
     * @param array $data Input data
     *
     * @return array
     */
    public function getInputErrors(array $data)
    {
        $errors = parent::getInputErrors($data);

        foreach ($this->getInputDataLabels() as $k => $t) {
            if (!isset($data[$k]) || !$data[$k]) {
                $errors[] = \XLite\Core\Translation::lbl('X field is required', array('field' => $t));
            }
        }

        return $errors;
    }

    /**
     * Get input data labels list
     *
     * @return array
     */
    protected function getInputDataLabels()
    {
        return array(
            'iid' => 'Select an issuer',
        );
    }

    /**
     * Get input data access levels list
     *
     * @return array
     */
    protected function getInputDataAccessLevels()
    {
        return array(
            'iid' => \XLite\Model\Payment\TransactionData::ACCESS_CUSTOMER,
        );
    }

    // }}}
}
