<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\Model\Payment\Processor;

use \XLite\Module\CDev\Paypal;

/**
 * Abstract Paypal (iframe) processor
 */
abstract class APaypal extends \XLite\Model\Payment\Base\Iframe
{
    /**
     * Request types definition
     */
    const REQ_TYPE_CAPTURE             = 'Capture';
    const REQ_TYPE_VOID                = 'Void';
    const REQ_TYPE_CREDIT              = 'Credit';

    /**
     * iframeURL
     *
     * @var string
     */
    protected $iframeURL = 'https://payflowlink.paypal.com/';

    /**
     * Partner code
     *
     * @var string
     */
    protected $partnerCode = 'XCART5_Cart';

    /**
     * Knowledge base page URL
     *
     * @var string
     */
    protected $knowledgeBasePageURL = '';

    /**
     * API Instance
     *
     * @var Paypal\Core\PayflowAPI
     */
    protected $api;

    // {{{ Common

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->api = new Paypal\Core\PayflowAPI();
    }

    /**
     * Get payment method row checkout template
     *
     * @param \XLite\Model\Payment\Method $method Payment method
     *
     * @return string
     */
    public function getCheckoutTemplate(\XLite\Model\Payment\Method $method)
    {
        return 'modules/CDev/Paypal/checkout/paypal.twig';
    }

    /**
     * Get payment method configuration page URL
     *
     * @param \XLite\Model\Payment\Method $method    Payment method
     * @param boolean                     $justAdded Flag if the method is just added via administration panel.
     *                                               Additional init configuration can be provided OPTIONAL
     *
     * @return string
     */
    public function getConfigurationURL(\XLite\Model\Payment\Method $method, $justAdded = false)
    {
        return \XLite\Core\Converter::buildURL('paypal_settings', '', array('method_id' => $method->getMethodId()));
    }

    /**
     * Payment method has settings into Module settings section
     *
     * @return boolean
     */
    public function hasModuleSettings()
    {
        return true;
    }

    /**
     * Return false to use own submit button on payment method settings form
     *
     * @return boolean
     */
    public function useDefaultSettingsFormButton()
    {
        return false;
    }

    /**
     * Get URL of referral page
     *
     * @param \XLite\Model\Payment\Method $method Payment method
     *
     * @return string
     */
    public function getPartnerPageURL(\XLite\Model\Payment\Method $method)
    {
        return \XLite::getXCartURL('http://www.x-cart.com/paypal_shopping_cart.html');
    }

    /**
     * Get knowledge base page URL
     *
     * @return string
     */
    public function getKnowledgeBasePageURL()
    {
        return $this->knowledgeBasePageURL;
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
        return $this->referralPageURL . $this->partnerCode;
    }

    /**
     * Prevent enabling Paypal Advanced if Paypal Standard is already enabled
     *
     * @param \XLite\Model\Payment\Method $method Payment method object
     *
     * @return boolean
     */
    public function canEnable(\XLite\Model\Payment\Method $method)
    {
        $result = parent::canEnable($method);

        if ($result && Paypal\Main::PP_METHOD_PPA === $method->getServiceName()) {
            $m = Paypal\Main::getPaymentMethod(Paypal\Main::PP_METHOD_PPS);
            $result = !($m && $m->isEnabled()) || $this->isForcedEnabled($method);
        }

        return $result;
    }

    /**
     * Get note with explanation why payment method can not be enabled
     *
     * @param \XLite\Model\Payment\Method $method Payment method object
     *
     * @return string
     */
    public function getForbidEnableNote(\XLite\Model\Payment\Method $method)
    {
        $result = parent::getForbidEnableNote($method);

        if (Paypal\Main::PP_METHOD_PPA === $method->getServiceName()) {
            $result = 'This payment method cannot be enabled together with PayPal Payments Standard method';
        }

        return $result;
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
            \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_VOID,
            \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_REFUND,
        );
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
            && $this->api->isConfigured();
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
     * Get return type of the iframe-method: html redirect with destroying an iframe
     *
     * @return string
     */
    public function getReturnType()
    {
        return static::RETURN_TYPE_HTML_REDIRECT_WITH_IFRAME_DESTROYING;
    }

    /**
     * Define saved into transaction data schema
     *
     * @return array
     */
    protected function defineSavedData()
    {
        $data = parent::defineSavedData();

        $data['TRANSTIME']                   = 'Transaction timestamp';
        $data['PNREF']                       = 'Unique Payflow transaction ID (PNREF)';
        $data['PPREF']                       = 'Unique PayPal transaction ID (PPREF)';  // PPA and PL
        $data['TYPE']                        = 'Transaction type';                      // PL
        $data['TRXTYPE']                     = 'Transaction type';                      // PPA and EC
        $data['RESULT']                      = 'Transaction result code (RESULT)';
        $data['RESPMSG']                     = 'Transaction result message (RESPMSG)';
        $data['CORRELATIONID']               = 'Tracking ID';                           // PPA and EC
        $data['FEEAMT']                      = 'Transaction fee';                       // EC
        $data['PENDINGREASON']               = 'Pending reason';                        // EC
        $data['PAYMENTINFO_0_TRANSACTIONID'] = 'Transaction ID';                        // EC

        return $data;
    }

    // }}}

    // {{{ Availability

    /**
     * Return true if current method is EC and PPA or PFL are enabled
     *
     * @param \XLite\Model\Payment\Method $method Payment method object
     *
     * @return boolean
     */
    public function isForcedEnabled(\XLite\Model\Payment\Method $method)
    {
        $result = parent::isForcedEnabled($method);

        if (!$result && Paypal\Main::PP_METHOD_EC === $method->getServiceName()) {
            $result = null !== $this->getParentMethod();
        }

        return $result;
    }

    /**
     * Get note with explanation why payment method was forcibly enabled
     *
     * @param \XLite\Model\Payment\Method $method Payment method
     *
     * @return string
     */
    public function getForcedEnabledNote(\XLite\Model\Payment\Method $method)
    {
        $result = parent::getForcedEnabledNote($method);

        if (!$result && Paypal\Main::PP_METHOD_EC === $method->getServiceName()) {
            if (null !== $this->getParentMethod()) {
                $result = 'Must be enabled as you use PayPal Payments Advanced or PayPal Payflow Link';
            }
        }

        return $result;
    }

    /**
     * Do something when payment method is enabled or disabled
     *
     * @param \XLite\Model\Payment\Method $method Payment method
     *
     * @return void
     */
    public function enableMethod(\XLite\Model\Payment\Method $method)
    {
        $methods = array(
            Paypal\Main::PP_METHOD_PPA,
            Paypal\Main::PP_METHOD_PFL,
        );

        // Add Express Checkout if the admin enables PPA or PFL methods
        if (in_array($method->getServiceName(), $methods, true) && $method->getEnabled()) {
            $m = Paypal\Main::getPaymentMethod(Paypal\Main::PP_METHOD_EC);

            if ($m) {
                $m->setAdded(true);
                $m->setEnabled(true);
            }
        }

        $methods[] = Paypal\Main::PP_METHOD_EC;

        // Add Paypal Credit if the admin enables PPA or PFL methods
        if (in_array($method->getServiceName(), $methods, true) && $method->getEnabled()) {
            $m = Paypal\Main::getPaymentMethod(Paypal\Main::PP_METHOD_PC);

            if ($m) {
                $m->setAdded(true);
                $m->setEnabled(true);
            }
        }
    }

    /**
     * Get payment method which forced enabling of Express Checkout
     *
     * @return \XLite\Model\Payment\Method
     */
    public function getParentMethod()
    {
        $result = null;

        $relatedMethods = array(
            Paypal\Main::PP_METHOD_PPA,
            Paypal\Main::PP_METHOD_PFL,
        );

        foreach ($relatedMethods as $rm) {
            $m = Paypal\Main::getPaymentMethod($rm);

            if ($m && $m->isEnabled()) {
                $result = $m;

                break;
            }
        }

        return $result;
    }

    // }}}

    // {{{ Transaction processing

    /**
     * Get initial transaction type (used when customer places order)
     *
     * @param \XLite\Model\Payment\Method $method Payment method object OPTIONAL
     *
     * @return string
     */
    public function getInitialTransactionType($method = null)
    {
        return 'A' === ($method ? $method->getSetting('transaction_type') : $this->getSetting('transaction_type'))
            ? \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_AUTH
            : \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_SALE;
    }

    /**
     * Update status of backend transaction related to an initial payment transaction
     *
     * @param \XLite\Model\Payment\Transaction $transaction Payment transaction
     * @param string                           $status      Transaction status
     *
     * @return void
     */
    public function updateInitialBackendTransaction(\XLite\Model\Payment\Transaction $transaction, $status)
    {
        $backendTransaction = $transaction->getInitialBackendTransaction();

        if (null !== $backendTransaction) {
            $backendTransaction->setStatus($status);
            $this->saveDataFromRequest($backendTransaction);
        }
    }

    /**
     * Get transaction message
     *
     * @param \XLite\Model\Payment\Transaction $transaction     Payment transaction (or backend transaction)
     * @param string                           $transactionType Type of transaction
     *
     * @return string
     */
    public function getTransactionMessage($transaction, $transactionType)
    {
        $message = parent::getTransactionMessage($transaction, $transactionType);

        $order = $transaction->getOrder();
        $currency = $order->getCurrency();

        $transactionSums = $order->getRawPaymentTransactionSums();
        $total = $order->getTotal();

        switch ($transactionType) {
            case 'capture': {
                if ($transactionSums['authorized'] != $total) {
                    $message = static::t(
                        'Paypal capture warning message',
                        array(
                            'authorized' => \XLite\View\AView::formatPrice($transactionSums['authorized'], $currency),
                            'total'      => \XLite\View\AView::formatPrice($total, $currency),
                        )
                    );
                }
                break;
            }
            default: {
            }
        }

        return $message;
    }

    // }}}

    // {{{ URL

    /**
     * Returns payment return url
     *
     * @return string
     */
    public function getPaymentReturnUrl()
    {
        return $this->getReturnURL(null, true);
    }

    /**
     * Returns payment cancel url
     *
     * @return string
     */
    public function getPaymentCancelUrl()
    {
        return $this->getReturnURL(null, true, true);
    }

    /**
     * Returns payment callback url
     *
     * @return string
     */
    public function getPaymentCallbackUrl()
    {
        return $this->getCallbackURL(null, true);
    }

    // }}}

    // {{{ Payment process

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

        Paypal\Main::addLog(
            'processReturn',
            \XLite\Core\Request::getInstance()->getData()
        );

        if (\XLite\Core\Request::getInstance()->cancel) {
            $this->setDetail(
                'status',
                'Customer has canceled checkout before completing their payments',
                'Status'
            );
            $transaction->setNote('Customer has canceled checkout before completing their payments');
            $transaction->setStatus($transaction::STATUS_CANCELED);

        } else {
            $request = \XLite\Core\Request::getInstance();

            $resultCode = null !== $request->RESULT ? (int) $request->RESULT : null;
            // https://developer.paypal.com/docs/classic/payflow/integration-guide/#secure-token-errors
            // https://developer.paypal.com/docs/classic/payflow/integration-guide/#result-values-and-respmsg-text
            if ($request->isPost() && null !== $resultCode /* && 0 !== $resultCode */
                && 160 !== $resultCode // ignore this error #XCN-4252
            ) {
                // Paypal returned customer directly to cart with some result

                $this->setDetail(
                    'status',
                    isset($request->RESPMSG) ? $request->RESPMSG : 'Unknown',
                    'Status'
                );

                $this->saveDataFromRequest();

                if (0 === $resultCode) {
                    // Transaction successful if RESULT == '0'
                    $status = $transaction::STATUS_SUCCESS;

                } elseif (126 === $resultCode // This RESULT returned if merchant enabled fraud filters
                                              // in their PayPal account
                    || 104 === $resultCode    // Timeout waiting for Processor response
                ) {
                    $status = $transaction::STATUS_PENDING;

                } else {
                    $status = $transaction::STATUS_FAILED;
                }

                $transaction->setStatus($status);
                $this->updateInitialBackendTransaction($transaction, $status);
            }
        }
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

        $request = \XLite\Core\Request::getInstance();
        $resultCode = null !== $request->RESULT ? (int) $request->RESULT : null;

        if (!$request->isPost()) {
            // Callback request must be POST
            $this->markCallbackRequestAsInvalid(static::t('Request type must be POST'));

        } elseif (null === $resultCode) {
            if (Paypal\Model\Payment\Processor\PaypalIPN::getInstance()->isCallbackIPN()) {
                // If callback is IPN request from Paypal
                Paypal\Model\Payment\Processor\PaypalIPN::getInstance()
                    ->processCallbackIPN($transaction, $this);

                $transaction->getOrder()->setPaymentStatusByTransaction($transaction);
                \XLite\Core\Database::getEM()->flush();

            } else {
                // RESULT parameter must be presented in all callback requests
                $this->markCallbackRequestAsInvalid(static::t('\'RESULT\' argument not found'));
            }

        } else {
            $this->setDetail(
                'status',
                isset($request->RESPMSG) ? $request->RESPMSG : 'Unknown',
                'Status'
            );

            $this->saveDataFromRequest();

            if (0 === $resultCode) {
                // Transaction successful if RESULT == '0'
                $status = $transaction::STATUS_SUCCESS;

            } elseif (126 === $resultCode // This RESULT returned if merchant enabled fraud filters
                // in their PayPal account
                || 104 === $resultCode    // Timeout waiting for Processor response
            ) {
                $status = $transaction::STATUS_PENDING;

            } else {
                $status = $transaction::STATUS_FAILED;
            }

            // Amount checking
            if (isset($request->AMT) && !$this->checkTotal($request->AMT)) {
                $status = $transaction::STATUS_FAILED;
            }

            $transaction->setStatus($status);
            $this->updateInitialBackendTransaction($transaction, $status);

            $transaction->registerTransactionInOrderHistory('callback');

            Paypal\Main::addLog(
                'processCallback',
                array(
                    'request' => $request,
                    'status' => $status
                )
            );
        }
    }

    // }}}

    // {{{ CreateSecureToken

    /**
     * Do CREATESECURETOKEN request and get SECURETOKEN from Paypal
     *
     * @return string
     */
    protected function doCreateSecureToken()
    {
        $token = null;

        $this->transaction->setPublicId($this->getTransactionId());

        $responseData = $this->doRequest('CreateSecureToken');

        if (!empty($responseData)) {
            if ($responseData['SECURETOKENID'] !== $this->api->getSecureTokenId()) {
                // It seems, a hack attempt detected, log this

            } elseif (!empty($responseData['SECURETOKEN'])) {
                $token = $responseData['SECURETOKEN'];
            }
        }

        return $token;
    }

    /**
     * Get array of parameters for CREATESECURETOKEN request
     *
     * @return array
     */
    protected function getCreateSecureTokenRequestParams()
    {
        $params = $this->api->convertCreateSecureTokenParams($this->getOrder());

        $orderNumber = $this->getTransactionId($this->getSetting('prefix'));
        $params['INVNUM'] = $orderNumber;

        return $params;
    }

    // }}}

    // {{{ Capture

    /**
     * Do 'CAPTURE' request on Authorized transaction.
     * Returns true on success or false on failure
     *
     * @param \XLite\Model\Payment\BackendTransaction $transaction Trandaction
     *
     * @return boolean
     */
    protected function doCapture(\XLite\Model\Payment\BackendTransaction $transaction)
    {
        $result = false;

        $responseData = $this->doRequest('Capture', $transaction);

        if (!empty($responseData)) {
            $status = \XLite\Model\Payment\Transaction::STATUS_FAILED;

            if (0 === (int) $responseData['RESULT']) {
                $result = true;
                $status = \XLite\Model\Payment\Transaction::STATUS_SUCCESS;

                \XLite\Core\TopMessage::getInstance()->addInfo('Payment has been captured successfully');

            } else {
                \XLite\Core\TopMessage::getInstance()
                    ->addError('Transaction failure. PayPal response: ' . $responseData['RESPMSG']);
            }

            $transaction->setStatus($status);
            $transaction->update();
        }

        return $result;
    }

    /**
     * Return array of parameters for 'CAPTURE' request
     *
     * @param \XLite\Model\Payment\BackendTransaction $transaction Transaction
     *
     * @return array
     */
    protected function getCaptureRequestParams(\XLite\Model\Payment\BackendTransaction $transaction)
    {
        $transactionId = $this->getTransactionReferenceId($transaction);

        return $this->api->convertCaptureParams($transaction, $transactionId);
    }

    /**
     * Get value for Capture transaction
     *
     * @param \XLite\Model\Payment\Transaction $transaction Transaction
     *
     * @return float
     */
    public function getCaptureTransactionValue($transaction)
    {
        return $this->api->getCaptureAmount($transaction);
    }

    // }}}

    // {{{ Void

    /**
     * Do 'VOID' request.
     * Returns true on success or false on failure
     *
     * @param \XLite\Model\Payment\BackendTransaction $transaction Transaction
     *
     * @return boolean
     */
    protected function doVoid(\XLite\Model\Payment\BackendTransaction $transaction)
    {
        $result = false;

        $responseData = $this->doRequest('Void', $transaction);

        if (!empty($responseData)) {
            $status = \XLite\Model\Payment\Transaction::STATUS_FAILED;

            if (0 === (int) $responseData['RESULT']) {
                $result = true;
                $status = \XLite\Model\Payment\Transaction::STATUS_SUCCESS;
                $transaction->getPaymentTransaction()->setStatus(\XLite\Model\Payment\Transaction::STATUS_VOID);
                \XLite\Core\TopMessage::getInstance()->addInfo('Payment have been voided successfully');

            } else {
                \XLite\Core\TopMessage::getInstance()
                    ->addError('Transaction failure. PayPal response: ' . $responseData['RESPMSG']);
            }

            $transaction->setStatus($status);
            $transaction->update();
        }

        return $result;
    }

    /**
     * Return array of parameters for 'VOID' request
     *
     * @param \XLite\Model\Payment\BackendTransaction $transaction Transaction
     *
     * @return array
     */
    protected function getVoidRequestParams(\XLite\Model\Payment\BackendTransaction $transaction)
    {
        $transactionId = $this->getTransactionReferenceId($transaction);

        return $this->api->convertVoidParams($transactionId);
    }

    // }}}

    // {{{ Credit (Refund)

    /**
     * Do 'CREDIT' request.
     * Returns true on success or false on failure
     *
     * @param \XLite\Model\Payment\BackendTransaction $transaction Transaction
     *
     * @return boolean
     */
    protected function doRefund(\XLite\Model\Payment\BackendTransaction $transaction)
    {
        $result = false;

        $responseData = $this->doRequest('Credit', $transaction);

        if (!empty($responseData)) {
            $status = \XLite\Model\Payment\Transaction::STATUS_FAILED;

            if (0 === (int) $responseData['RESULT']) {
                $result = true;
                $status = \XLite\Model\Payment\Transaction::STATUS_SUCCESS;

                \XLite\Core\TopMessage::getInstance()->addInfo('Payment has been refunded successfully');

            } else {
                \XLite\Core\TopMessage::getInstance()
                    ->addError('Transaction failure. PayPal response: ' . $responseData['RESPMSG']);
            }

            $transaction->setStatus($status);
            $transaction->update();
        }

        return $result;
    }

    /**
     * Return array of parameters for 'CREDIT' request
     *
     * @param \XLite\Model\Payment\BackendTransaction $transaction Transaction
     *
     * @return array
     */
    protected function getCreditRequestParams(\XLite\Model\Payment\BackendTransaction $transaction)
    {
        $transactionId = $this->getTransactionReferenceId($transaction);

        return $this->api->convertCreditParams($transaction, $transactionId);
    }

    /**
     * Get value for Refund transaction
     *
     * @param \XLite\Model\Payment\Transaction $transaction Transaction
     *
     * @return float
     */
    public function getRefundTransactionValue($transaction)
    {
        return $this->api->getRefundAmount($transaction);
    }

    // }}}

    // {{{ IFrame

    /**
     * Get URL of the page to display within iframe
     *
     * @return string
     */
    protected function getIframeData()
    {
        $token = $this->doCreateSecureToken();

        $result = $token ? $this->getPostURL($this->iframeURL, $this->getIframeParams($token)) : null;

        Paypal\Main::addLog(
            'getIframeData()',
            $result
        );

        return $result;
    }

    /**
     * Get post URL
     *
     * @param string $postURL URL OPTIONAL
     * @param array  $params  Array of URL parameters OPTIONAL
     *
     * @return string
     */
    protected function getPostURL($postURL, $params = array())
    {
        $args = !empty($params) ? '?' . implode('&', $params) : '';

        return $postURL . $args;
    }

    /**
     * Get iframe size
     *
     * @return array
     */
    protected function getIframeSize()
    {
        return array(610, 610);
    }

    /**
     * Returns the list of iframe URL arguments
     *
     * @param string $token Token
     *
     * @return array
     */
    protected function getIframeParams($token)
    {
        $params = array(
            'SECURETOKEN=' . $token,
            'SECURETOKENID=' . $this->api->getSecureTokenId(),
        );

        if ($this->isTestMode($this->transaction->getPaymentMethod())) {
            $params[] = 'MODE=TEST';
        }

        return $params;
    }

    // }}}

    // {{{ Backend request

    /**
     * Do HTTPS request to Paypal server with data set depended on $requestType.
     * Returns an array represented a parsed response from Paypal
     *
     * @param string                                  $requestType Type of request
     * @param \XLite\Model\Payment\BackendTransaction $transaction Backend transaction object OPTIONAL
     *
     * @return array
     */
    protected function doRequest($requestType, $transaction = null)
    {
        if (null === $this->transaction && null !== $transaction) {
            $this->transaction = $transaction;
        }

        if (null === $transaction) {
            $transaction = $this->transaction;
        }

        $method = sprintf('get%sRequestParams', ucfirst($requestType));
        if (method_exists($this, $method)) {
            $params = $this->{$method}($transaction);
        }

        $result = $this->api->doRequest($requestType, $params);
        $response = $this->api->getLastResponse();

        if (200 === (int) $response->code
            && !empty($response->body)
            && !empty($transaction)
            && !empty($result)
        ) {
            $this->saveFilteredData($result, $transaction);

            \XLite\Core\Database::getEM()->flush();
        }

        return $result;
    }

    // }}}

    // {{{ Reference id

    /**
     * Get reference ID field name for backend transactions
     *
     * @return string
     */
    protected function getReferenceIdField()
    {
        return 'PNREF';
    }

    /**
     * Get reference ID of parent transaction
     * (e.g. get PNREF of AUTH transaction for request a CAPTURE transaction)
     *
     * @param \XLite\Model\Payment\BackendTransaction $backendTransaction Backend transaction object
     *
     * @return string
     */
    protected function getTransactionReferenceId(\XLite\Model\Payment\BackendTransaction $backendTransaction)
    {
        $referenceId = null;

        $paymentTransaction = $backendTransaction->getPaymentTransaction();

        switch ($backendTransaction->getType()) {
            case \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_CAPTURE:
            case \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_VOID:
                if (\XLite\Model\Payment\BackendTransaction::TRAN_TYPE_AUTH === $paymentTransaction->getType()) {
                    $referenceId = $paymentTransaction->getDataCell($this->getReferenceIdField())->getValue();
                }

                break;

            case \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_REFUND:
                if (\XLite\Model\Payment\BackendTransaction::TRAN_TYPE_SALE === $paymentTransaction->getType()) {
                    $referenceId = $paymentTransaction->getDataCell($this->getReferenceIdField())->getValue();

                } elseif ($paymentTransaction->isCaptured()) {
                    foreach ($paymentTransaction->getBackendTransactions() as $bt) {
                        if (\XLite\Model\Payment\BackendTransaction::TRAN_TYPE_CAPTURE === $bt->getType()
                            && \XLite\Model\Payment\Transaction::STATUS_SUCCESS === $bt->getStatus()
                        ) {
                            $referenceId = $bt->getDataCell($this->getReferenceIdField())->getValue();

                            break;
                        }
                    }
                }
                break;

            default:
                break;
        }

        return $referenceId;
    }

    // }}}
}
