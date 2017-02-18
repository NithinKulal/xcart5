<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\Amazon\PayWithAmazon\Model\Payment\Processor;

use XLite\Core\TopMessage;
use XLite\Model\Payment\BackendTransaction;
use XLite\Model\Payment\Transaction;
use XLite\Module\Amazon\PayWithAmazon\Core\APIException;
use XLite\Module\Amazon\PayWithAmazon\Main;

/**
 * PayWithAmazon processor
 */
class PayWithAmazon extends \XLite\Model\Payment\Base\CreditCard
{
    public $invalidPaymentMethod = false;

    /**
     * @var array
     */
    protected static $declineReasons = [
        'InvalidPaymentMethod' => 'There were problems with the payment method.',
        'AmazonRejected'       => 'Amazon has rejected the authorisation.',
        'ProcessingFailure'    => 'Amazon could not process the transaction due to an internal processing error.',
    ];

    /**
     * @var array
     */
    protected $jsUrls = [
        'test' => [
            'EUR' => 'https://static-eu.payments-amazon.com/OffAmazonPayments/de/sandbox/js/Widgets.js',
            'GBR' => 'https://static-eu.payments-amazon.com/OffAmazonPayments/uk/sandbox/js/Widgets.js',
            'USD' => 'https://static-na.payments-amazon.com/OffAmazonPayments/us/sandbox/js/Widgets.js',
        ],
        'live' => [
            'EUR' => 'https://static-eu.payments-amazon.com/OffAmazonPayments/de/js/Widgets.js',
            'GBR' => 'https://static-eu.payments-amazon.com/OffAmazonPayments/uk/js/Widgets.js',
            'USD' => 'https://static-na.payments-amazon.com/OffAmazonPayments/us/js/Widgets.js',
        ],
    ];

    /**
     * @var array
     */
    protected $ipnData = [];

    /**
     * Get allowed backend transactions
     * @todo: check for partial/multi refund
     *
     * @return string[] Status code
     */
    public function getAllowedTransactions()
    {
        return [
            BackendTransaction::TRAN_TYPE_CAPTURE,
            BackendTransaction::TRAN_TYPE_VOID,
            BackendTransaction::TRAN_TYPE_REFUND,
            'amazonRefresh',
            'amazonRefundRefresh',
        ];
    }

    /**
     * @return string Widget class name or template path
     */
    public function getSettingsWidget()
    {
        return 'modules/Amazon/PayWithAmazon/config.twig';
    }

    /**
     * @param \XLite\Model\Payment\Method $method Payment method
     *
     * @return string
     */
    public function getJsSdkUrl($method)
    {
        $mode     = $this->isTestMode($method) ? 'test' : 'live';
        $currency = $method->getSetting('merchant_id');
        $currency = in_array($currency, ['EUR', 'GBR'], true)
            ? $currency
            : 'USD';
        $sid      = $method->getSetting('merchant_id');

        return $this->jsUrls[$mode][$currency] . '?sellerId=' . $sid;
    }

    /**
     * @param \XLite\Model\Payment\Method $method Payment method
     *
     * @return string|boolean|null
     */
    public function getAdminIconURL(\XLite\Model\Payment\Method $method)
    {
        return true;
    }

    /**
     * @param \XLite\Model\Payment\Method $method Payment method
     *
     * @return boolean
     */
    public function isConfigured(\XLite\Model\Payment\Method $method)
    {
        return parent::isConfigured($method)
        && $method->getSetting('merchant_id')
        && $method->getSetting('client_id')
        && \XLite\Core\Config::getInstance()->Security->customer_security;
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
        return 'A' === ($method ? $method->getSetting('capture_mode') : $this->getSetting('capture_mode'))
            ? BackendTransaction::TRAN_TYPE_AUTH
            : BackendTransaction::TRAN_TYPE_SALE;
    }

    /**
     * Get allowed currencies
     *
     * @param \XLite\Model\Payment\Method $method Payment method
     *
     * @return array
     */
    protected function getAllowedCurrencies(\XLite\Model\Payment\Method $method)
    {
        return ['USD', 'GBR', 'EUR', 'JPY'];
    }

    /**
     * @param \XLite\Model\Payment\Transaction $transaction     Payment transaction (or backend transaction)
     * @param string                           $transactionType Type of transaction
     *
     * @return string
     */
    public function getTransactionMessage($transaction, $transactionType)
    {
        switch ($transactionType) {
            case 'amazonRefresh':
                $status  = $transaction->getDetail('authorizationStatus');
                $message = static::t('Authorization status: {{status}}', ['status' => $status]);
                break;
            case 'amazonRefundRefresh':
                $status  = $transaction->getDetail('refundStatus');
                $message = static::t('Refund status: {{status}}', ['status' => $status]);
                break;
            default:
                $message = null;
                break;
        }

        return $message;
    }

    /**
     * Do initial payment
     *
     * @return string Status code
     */
    protected function doInitialPayment()
    {
        $result = static::FAILED;

        $request        = \XLite\Core\Request::getInstance();
        $orderReference = $request->orderReference;

        $transactionData = [
            'amazonOrderReferenceId' => $orderReference,
        ];

        if ($this->getSetting('capture_mode') === 'C') {
            $authorizationData['capture_now'] = 'true';
        }

        $currency = $this->transaction->getCurrency();
        $amount   = $this->transaction->getValue();

        $authorizationDetails = [];

        $errorMessage = 'Unexpected authorize reply';

        try {
            if (!$request->isRetry) {
                $this->clientRequest(
                    'setOrderReferenceDetails',
                    [
                        'amazon_order_reference_id' => $orderReference,
                        'amount'                    => $amount,
                        'platform_id'               => Main::PLATFORM_ID,
                        'seller_note'               => '',
                        'seller_order_id'           => $this->getTransactionId(),
                        'store_name'                => \XLite\Core\Config::getInstance()->Company->company_name,
                    ]
                );
            }

            $this->clientRequest(
                'confirmOrderReference',
                [
                    'amazon_order_reference_id' => $orderReference,
                ]
            );

            $details = $this->clientRequest(
                'getOrderReferenceDetails',
                [
                    'amazon_order_reference_id' => $orderReference,
                ]
            );

            $controller = \XLite::getController();
            if ($details && $controller instanceof \XLite\Module\Amazon\PayWithAmazon\Controller\Customer\AmazonCheckout) {
                $address = $this->getAddressDataFromOrderReferenceDetails($details);
                if ($address) {
                    $controller->updateAddress($address);
                }
            }

            $authorizationData = [
                'amazon_order_reference_id'  => $orderReference,
                'authorization_amount'       => $amount,
                'authorization_reference_id' => 'auth_' . $this->getTransactionId(),
                'seller_authorization_note'  => '',
            ];

            if ($this->getSetting('capture_mode') === 'C') {
                $authorizationData['capture_now'] = 'true';
            }

            $customerNotes = $request->notes;
            if ($customerNotes && \XLite\View\FormField\Select\TestLiveMode::TEST === $this->getSetting('mode')) {
                $authorizationData['seller_authorization_note'] = $customerNotes;
            }

            if ($this->getSetting('sync_mode') === 'S') {
                $authorizationData['transaction_timeout'] = '0';
            }

            $response             = $this->clientRequest('authorize', $authorizationData);
            $authorizationDetails = isset($response['AuthorizeResult']['AuthorizationDetails'])
                ? $response['AuthorizeResult']['AuthorizationDetails']
                : [];

        } catch (APIException $e) {
            $errorMessage = $e->getMessage();
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
        }

        if ($authorizationDetails) {
            $authorizationStatus = $authorizationDetails['AuthorizationStatus']['State'];
            $authorizationReason = isset($authorizationDetails['AuthorizationStatus']['ReasonCode'])
                ? $authorizationDetails['AuthorizationStatus']['ReasonCode']
                : '';
            $softDecline         = $authorizationDetails['SoftDecline'] === 'true';

            $transactionData['authorizationStatus']   = $authorizationStatus;
            $transactionData['authorizationReason']   = $authorizationReason;
            $transactionData['amazonAuthorizationId'] = $authorizationDetails['AmazonAuthorizationId'];

            if ($authorizationReason === 'InvalidPaymentMethod' && $softDecline) {
                $this->invalidPaymentMethod = true;
            }

            switch ($authorizationStatus) {
                case 'Declined':
                    $result = static::FAILED;

                    if (isset(static::$declineReasons[$authorizationReason])) {
                        $errorMessage = static::t(static::$declineReasons[$authorizationReason]);
                    }

                    break;
                case 'Pending':
                    $result = static::PENDING;

                    break;
                case 'Open':
                    $result = static::COMPLETED;

                    break;
                case 'Closed':
                    $result = static::COMPLETED;

                    $transactionData['amazonCaptureId'] = $authorizationDetails['IdList']['member'];

                    break;
            }

            $backendTransaction = $this->transaction->createBackendTransaction(
                $this->getSetting('capture_mode') === 'C'
                    ? BackendTransaction::TRAN_TYPE_SALE
                    : BackendTransaction::TRAN_TYPE_AUTH
            );

            $backendTransaction->setStatus($result);
        }

        if ($errorMessage) {
            $this->transaction->setNote($errorMessage);
            $this->transaction->setDataCell('status', $errorMessage);
        }

        $this->saveFilteredData($transactionData);

        return $result;
    }

    /**
     * @param array $orderReferenceDetails
     *
     * @return array
     */
    public function getAddressDataFromOrderReferenceDetails($orderReferenceDetails)
    {
        $address         = [];
        $responseAddress = isset($orderReferenceDetails['GetOrderReferenceDetailsResult']['OrderReferenceDetails']['Destination']['PhysicalDestination'])
            ? $orderReferenceDetails['GetOrderReferenceDetailsResult']['OrderReferenceDetails']['Destination']['PhysicalDestination']
            : [];

        if ($responseAddress) {
            $address['zipcode']      = $responseAddress['PostalCode'];
            $address['country_code'] = $responseAddress['CountryCode'];
            $address['city']         = $responseAddress['City'];

            $state = \XLite\Core\Database::getRepo('XLite\Model\State')
                ->findOneByCountryAndState($address['country_code'], $responseAddress['StateOrRegion']);

            if ($state) {

                $address['state_id'] = $state->getStateId();

            } elseif (!empty($responseAddress['StateOrRegion'])) {

                $address['custom_state'] = $responseAddress['StateOrRegion'];
            }

            if (!empty($responseAddress['Phone'])) {
                $address['phone'] = $responseAddress['Phone'];
            }

            if (!empty($responseAddress['AddressLine1'])) {
                $address['street'] = $responseAddress['AddressLine1'];
                if (!empty($responseAddress['AddressLine2'])) {
                    $address['street'] .= ' ' . $responseAddress['AddressLine2'];
                }
            }

            if (!empty($responseAddress['Name'])) {
                list($address['firstname'], $address['lastname']) = explode(' ', $responseAddress['Name'], 2);
                if (empty($address['lastname'])) {
                    // XC does not support single word customer name
                    $address['lastname'] = $address['firstname'];
                }
            }
        }

        return $address;
    }

    /**
     * @param Transaction $transaction
     *
     * @return boolean
     */
    protected function isCaptureTransactionAllowed(Transaction $transaction)
    {
        return $transaction->isCaptureTransactionAllowed()
        && (bool) $transaction->getDetail('amazonAuthorizationId');
    }

    /**
     * @param BackendTransaction $transaction Transaction
     *
     * @return boolean
     */
    protected function doCapture(BackendTransaction $transaction)
    {
        $result = false;

        $paymentTransaction = $transaction->getPaymentTransaction();
        $captureDetails     = [];
        $errorMessage       = 'Unexpected capture reply';

        try {
            $response = $this->clientRequest(
                'capture',
                [
                    'amazon_authorization_id' => $paymentTransaction->getDetail('amazonAuthorizationId'),
                    'capture_amount'          => $transaction->getValue(),
                    'capture_reference_id'    => 'capture_' . $this->getTransactionId(),
                    'seller_capture_note'     => '',
                ]
            );

            $captureDetails = isset($response['CaptureResult']['CaptureDetails'])
                ? $response['CaptureResult']['CaptureDetails']
                : [];

        } catch (APIException $e) {
            $errorMessage = $e->getMessage();
        }

        if ($captureDetails) {
            $status = $captureDetails['CaptureStatus']['State'];
            if ($status === 'Completed') {
                $transaction->setStatus(BackendTransaction::STATUS_SUCCESS);

                $amazonCaptureId = $captureDetails['AmazonCaptureId'];
                $paymentTransaction->setDataCell('amazonCaptureId', $amazonCaptureId, 'AmazonCaptureId identifier');
                $paymentTransaction->setDataCell('authorizationStatus', $status, 'Current status of the authorization');

                $result = true;
                TopMessage::addInfo('Payment has been captured successfully.');

            } else {
                $errorMessage = 'Status = ' . $status;
            }
        }

        if (!$result) {
            TopMessage::addWarning('Payment capture error: {{error}}', ['error' => $errorMessage]);
        }

        return $result;
    }

    /**
     * @param Transaction $transaction
     *
     * @return boolean
     */
    protected function isVoidTransactionAllowed(Transaction $transaction)
    {
        return $transaction->isVoidTransactionAllowed()
        && (bool) $transaction->getDetail('amazonAuthorizationId');
    }

    /**
     * @param BackendTransaction $transaction Transaction
     *
     * @return boolean
     */
    protected function doVoid(BackendTransaction $transaction)
    {
        $result = false;

        $paymentTransaction = $transaction->getPaymentTransaction();
        $errorMessage       = 'Unexpected void reply';
        $response           = [];

        try {
            $response = $this->clientRequest(
                'closeAuthorization',
                [
                    'amazon_authorization_id' => $paymentTransaction->getDetail('amazonAuthorizationId'),
                    'closure_reason'          => '',
                ]
            );
        } catch (APIException $e) {
            $errorMessage = $e->getMessage();
        }

        if ($response) {
            $transaction->setStatus(BackendTransaction::STATUS_SUCCESS);
            $paymentTransaction->setStatus(Transaction::STATUS_VOID);

            $result = true;
            TopMessage::addInfo('Payment have been voided successfully.');

        } else {
            TopMessage::addWarning('Payment void error: {{error}}', ['error' => $errorMessage]);
        }

        return $result;
    }

    /**
     * @param Transaction $transaction
     *
     * @return boolean
     */
    protected function isRefundTransactionAllowed(Transaction $transaction)
    {
        return $transaction->isRefundTransactionAllowed()
        && (bool) $transaction->getDetail('amazonCaptureId')
        && !(bool) $transaction->getDetail('amazonRefundId');
    }

    /**
     * @param BackendTransaction $transaction Transaction
     *
     * @return boolean
     */
    protected function doRefund(BackendTransaction $transaction)
    {
        $result = false;


        $paymentTransaction = $transaction->getPaymentTransaction();
        $refundDetails      = [];
        $errorMessage       = '';

        try {
            $response = $this->clientRequest(
                'refund',
                [
                    'amazon_capture_id'   => $paymentTransaction->getDetail('amazonCaptureId'),
                    'refund_amount'       => $transaction->getValue(),
                    'refund_reference_id' => 'refund_' . $this->getTransactionId(),
                    'seller_capture_note' => '',
                ]
            );

            $refundDetails = isset($response['RefundResult']['RefundDetails'])
                ? $response['RefundResult']['RefundDetails']
                : [];
        } catch (APIException $e) {
            $errorMessage = $e->getMessage();
        }

        if ($refundDetails) {
            $status = $refundDetails['RefundStatus']['State'];
            if ($status === 'Completed' || $status === 'Pending') {
                $transaction->setStatus(
                    $status === 'Completed'
                        ? BackendTransaction::STATUS_SUCCESS
                        : BackendTransaction::STATUS_PENDING
                );

                $amazonRefundId = $refundDetails['AmazonRefundId'];
                $paymentTransaction->setDataCell('amazonRefundId', $amazonRefundId, 'AmazonRefundId identifier');
                $paymentTransaction->setDataCell('refundStatus', $status, 'Current status of the refund');

                $result = true;
                TopMessage::addInfo(
                    $status === 'Completed'
                        ? 'Payment has been refunded successfully.'
                        : 'Refund is in progress...'
                );

            } else {
                $errorMessage = 'Status = ' . $status;
            }
        } else {
            $errorMessage = 'Unexpected refund reply';
        }

        if (!$result) {
            TopMessage::addWarning('Payment refund error: {{error}}', ['error' => $errorMessage]);
        }

        return $result;
    }

    /**
     * @param Transaction $transaction
     *
     * @return boolean
     */
    protected function isAmazonRefreshTransactionAllowed(Transaction $transaction)
    {
        return 'Pending' === $transaction->getDetail('authorizationStatus');
    }

    /**
     * @param BackendTransaction $transaction Transaction
     *
     * @return boolean
     */
    protected function doAmazonRefresh(BackendTransaction $transaction)
    {
        $result = false;

        $paymentTransaction   = $transaction->getPaymentTransaction();
        $authorizationDetails = [];
        $errorMessage         = 'Unexpected refresh reply';

        $authTransaction = null;
        /** @var BackendTransaction $backendTransaction */
        foreach ($paymentTransaction->getBackendTransactions() as $backendTransaction) {
            if ($backendTransaction->getType() === BackendTransaction::TRAN_TYPE_AUTH) {
                $authTransaction = $backendTransaction;
                break;
            }
        }

        $saleTransaction = null;
        /** @var BackendTransaction $backendTransaction */
        foreach ($paymentTransaction->getBackendTransactions() as $backendTransaction) {
            if ($backendTransaction->getType() === BackendTransaction::TRAN_TYPE_SALE) {
                $saleTransaction = $backendTransaction;
                break;
            }
        }

        try {
            $response = $this->clientRequest(
                'getAuthorizationDetails',
                [
                    'amazon_authorization_id' => $paymentTransaction->getDetail('amazonAuthorizationId'),
                ]
            );

            $authorizationDetails = isset($response['GetAuthorizationDetailsResult']['AuthorizationDetails'])
                ? $response['GetAuthorizationDetailsResult']['AuthorizationDetails']
                : [];

        } catch (APIException $e) {
            $errorMessage = $e->getMessage();
        }

        if ($authorizationDetails) {
            $status = $authorizationDetails['AuthorizationStatus']['State'];

            switch ($status) {
                case 'Open':
                    if ($authTransaction
                        && $paymentTransaction->getType() === BackendTransaction::TRAN_TYPE_AUTH
                        && $paymentTransaction->getStatus() === Transaction::STATUS_PENDING
                    ) {
                        $paymentTransaction->setStatus(Transaction::STATUS_SUCCESS);
                        $authTransaction->setStatus(Transaction::STATUS_SUCCESS);
                        $transaction->setStatus(Transaction::STATUS_SUCCESS);

                        $paymentTransaction->setDataCell('authorizationStatus', $status, 'Current status of the authorization');

                        TopMessage::addInfo('Payment has been authorized successfully.');
                        $result = true;
                    }
                    break;
                case 'Closed':
                    $authorizedAmount = $authorizationDetails['AuthorizationAmount']['Amount'];
                    $capturedAmount   = $authorizationDetails['CapturedAmount']['Amount'];

                    if ($saleTransaction && $capturedAmount && $capturedAmount === $authorizedAmount) {
                        $paymentTransaction->setStatus(Transaction::STATUS_SUCCESS);
                        $saleTransaction->setStatus(Transaction::STATUS_SUCCESS);
                        $transaction->setStatus(Transaction::STATUS_SUCCESS);

                        $amazonCaptureId = $authorizationDetails['IdList']['member'];
                        $paymentTransaction->setDataCell('amazonCaptureId', $amazonCaptureId, 'AmazonCaptureId identifier');
                        $paymentTransaction->setDataCell('authorizationStatus', $status, 'Current status of the authorization');

                        TopMessage::addInfo('Payment has been captured successfully.');
                        $result = true;
                    }
                    break;
                case 'Declined':
                    $paymentTransaction->setStatus(Transaction::STATUS_FAILED);
                    if ($authTransaction
                        && $paymentTransaction->getType() === BackendTransaction::TRAN_TYPE_AUTH
                    ) {
                        $authTransaction->setStatus(Transaction::STATUS_FAILED);
                    }

                    if ($saleTransaction
                        && $saleTransaction->getType() === BackendTransaction::TRAN_TYPE_AUTH
                    ) {
                        $saleTransaction->setStatus(Transaction::STATUS_FAILED);
                    }

                    $transaction->setStatus(Transaction::STATUS_SUCCESS);

                    $paymentTransaction->setDataCell('authorizationStatus', $status, 'Current status of the authorization');

                    TopMessage::addInfo('Payment has been declined.');
                    $result = true;
                    break;
                case 'Pending':
                    $transaction->setStatus(Transaction::STATUS_SUCCESS);
                    TopMessage::addInfo('Payment transaction is in progress...');
                    break;
                default:
                    $errorMessage = 'Status = ' . $status;
                    break;
            }
        }

        if (!$result) {
            TopMessage::addWarning('Payment refresh error: {{error}}', ['error' => $errorMessage]);
        }

        return $result;
    }

    /**
     * @param Transaction $transaction
     *
     * @return boolean
     */
    protected function isAmazonRefundRefreshTransactionAllowed(Transaction $transaction)
    {
        $refundTransaction = null;
        /** @var BackendTransaction $backendTransaction */
        foreach ($transaction->getBackendTransactions() as $backendTransaction) {
            if ($backendTransaction->getType() === BackendTransaction::TRAN_TYPE_REFUND
                && $backendTransaction->getStatus() === BackendTransaction::STATUS_PENDING
            ) {
                $refundTransaction = $backendTransaction;
                break;
            }
        }

        return $refundTransaction && 'Pending' === $transaction->getDetail('refundStatus');
    }

    /**
     * @param BackendTransaction $transaction Transaction
     *
     * @return boolean
     */
    protected function doAmazonRefundRefresh(BackendTransaction $transaction)
    {
        $result = false;

        $paymentTransaction = $transaction->getPaymentTransaction();
        $refundDetails      = [];
        $errorMessage       = 'Unexpected refresh refund reply';

        $refundTransaction = null;
        /** @var BackendTransaction $backendTransaction */
        foreach ($paymentTransaction->getBackendTransactions() as $backendTransaction) {
            if ($backendTransaction->getType() === BackendTransaction::TRAN_TYPE_REFUND
                && $backendTransaction->getStatus() === BackendTransaction::STATUS_PENDING
            ) {
                $refundTransaction = $backendTransaction;
                break;
            }
        }

        try {
            $response = $this->clientRequest(
                'getRefundDetails',
                [
                    'amazon_refund_id' => $paymentTransaction->getDetail('amazonRefundId'),
                ]
            );

            $refundDetails = isset($response['GetRefundDetailsResult']['RefundDetails'])
                ? $response['GetRefundDetailsResult']['RefundDetails']
                : [];

        } catch (APIException $e) {
            $errorMessage = $e->getMessage();
        }

        if ($refundDetails) {
            $status = $refundDetails['RefundStatus']['State'];

            switch ($status) {
                case 'Completed':
                    $refundTransaction->setStatus(BackendTransaction::STATUS_SUCCESS);
                    $transaction->setStatus(BackendTransaction::STATUS_SUCCESS);

                    $paymentTransaction->setDataCell('refundStatus', $status, 'Current status of the refund');

                    $result = true;
                    TopMessage::addInfo('Payment has been refunded successfully.');
                    break;
                case 'Pending':
                    $transaction->setStatus(Transaction::STATUS_SUCCESS);
                    TopMessage::addInfo('Refund is in progress...');
                    break;
                default:
                    $errorMessage = 'Status = ' . $status;
                    break;
            }
        }

        if (!$result) {
            TopMessage::addWarning('Payment refund error: {{error}}', ['error' => $errorMessage]);
        }

        return $result;
    }

    /**
     * Get callback request owner transaction or null
     *
     * @return Transaction|null
     */
    public function getCallbackOwnerTransaction()
    {
        $result = null;

        $headers     = getallheaders();
        $requestBody = file_get_contents('php://input');

        $ipnData  = [];
        $publicId = '';

        try {
            Main::includeIPNHandler();

            $ipnHandler = new \PayWithAmazon\IpnHandler($headers, $requestBody);
            $ipnData    = $ipnHandler->toArray();

            /** @tricky: because we can synchronize order processing in this place */
            sleep(5);

            Main::log(
                [
                    'message' => __FUNCTION__,
                    'headers' => $headers,
                    'request' => $requestBody,
                    'data'    => $ipnData,
                ]
            );

        } catch (\Exception $e) {
        }

        if (isset($ipnData['NotificationReferenceId'], $ipnData['NotificationType'])) {
            switch ($ipnData['NotificationType']) {
                case 'PaymentAuthorize':
                    $method = Main::getMethod();
                    /** @tricky: ignore authorization IPN if capture mode is Auth+Capture(Sale) */
                    if ($method->getSetting('capture_mode') === 'A') {
                        $authorizationReferenceId = isset($ipnData['AuthorizationDetails']['AuthorizationReferenceId'])
                            ? $ipnData['AuthorizationDetails']['AuthorizationReferenceId']
                            : '';

                        $publicId = str_replace('auth_', '', $authorizationReferenceId);
                    }
                    break;
                case 'PaymentCapture':
                    $captureReferenceId = isset($ipnData['CaptureDetails']['CaptureReferenceId'])
                        ? $ipnData['CaptureDetails']['CaptureReferenceId']
                        : '';

                    $publicId = str_replace(['auth_', 'capture_'], ['', ''], $captureReferenceId);
                    //$publicId = preg_replace('/_\d+$/', '', $publicId);
                    break;
                case 'PaymentRefund':
                    $refundReferenceId = isset($ipnData['RefundDetails']['RefundReferenceId'])
                        ? $ipnData['RefundDetails']['RefundReferenceId']
                        : '';

                    $publicId = str_replace('refund_', '', $refundReferenceId);
                    //$publicId = preg_replace('/_\d+$/', '', $publicId);
                    break;
            }
        }

        if ($publicId) {
            /** @var Transaction $result */
            $result        = \Xlite\Core\Database::getRepo('XLite\Model\Payment\Transaction')->findOneBy(
                ['public_id' => $publicId]
            );
            $this->ipnData = $ipnData;
        }

        return $result;
    }

    /**
     * @param Transaction $transaction Callback-owner transaction
     */
    public function processCallback(Transaction $transaction)
    {
        parent::processCallback($transaction);

        if (!$this->ipnData) {
            return;
        }

        $result             = false;
        $historyTransaction = null;

        $ipnData = $this->ipnData;
        switch ($ipnData['NotificationType']) {
            case 'PaymentAuthorize':
                $authorizationDetails = isset($ipnData['AuthorizationDetails'])
                    ? $ipnData['AuthorizationDetails']
                    : [];

                if ($authorizationDetails) {
                    $status = $authorizationDetails['AuthorizationStatus']['State'];

                    $authTransaction = null;
                    /** @var BackendTransaction $backendTransaction */
                    foreach ($transaction->getBackendTransactions() as $backendTransaction) {
                        if ($backendTransaction->getType() === BackendTransaction::TRAN_TYPE_AUTH) {
                            $authTransaction = $backendTransaction;
                            break;
                        }
                    }

                    $voidTransaction = null;
                    /** @var BackendTransaction $backendTransaction */
                    foreach ($transaction->getBackendTransactions() as $backendTransaction) {
                        if ($backendTransaction->getType() === BackendTransaction::TRAN_TYPE_VOID
                            && ($backendTransaction->getStatus() === BackendTransaction::STATUS_PENDING
                                || $backendTransaction->getStatus() === BackendTransaction::STATUS_SUCCESS)
                        ) {
                            $voidTransaction = $backendTransaction;
                            break;
                        }
                    }

                    $saleTransaction = null;
                    /** @var BackendTransaction $backendTransaction */
                    foreach ($transaction->getBackendTransactions() as $backendTransaction) {
                        if ($backendTransaction->getType() === BackendTransaction::TRAN_TYPE_SALE) {
                            $saleTransaction = $backendTransaction;
                            break;
                        }
                    }

                    switch ($status) {
                        case 'Open':
                            if ($authTransaction
                                && $transaction->getType() === BackendTransaction::TRAN_TYPE_AUTH
                            ) {
                                if ($transaction->getStatus() === Transaction::STATUS_PENDING) {
                                    $transaction->setStatus(Transaction::STATUS_SUCCESS);
                                    $authTransaction->setStatus(Transaction::STATUS_SUCCESS);
                                    $transaction->setDataCell('authorizationStatus', $status, 'Current status of the authorization');
                                }

                                $historyTransaction = $authTransaction;
                                $result             = true;
                            }
                            break;
                        case 'Closed':
                            if (!$voidTransaction) {
                                $voidTransaction = $transaction->createBackendTransaction(BackendTransaction::TRAN_TYPE_VOID);
                            }

                            if ($voidTransaction->getStatus() === Transaction::STATUS_PENDING) {
                                $transaction->setStatus(Transaction::STATUS_VOID);
                                $voidTransaction->setStatus(Transaction::STATUS_SUCCESS);
                                $transaction->setDataCell('authorizationStatus', $status, 'Current status of the authorization');
                            }

                            $historyTransaction = $voidTransaction;
                            $result             = true;
                            break;
                        case 'Declined':
                            $transaction->setStatus(Transaction::STATUS_FAILED);
                            if ($authTransaction
                                && $transaction->getType() === BackendTransaction::TRAN_TYPE_AUTH
                            ) {
                                $authTransaction->setStatus(Transaction::STATUS_FAILED);
                                $historyTransaction = $authTransaction;
                            }

                            if ($saleTransaction
                                && $saleTransaction->getType() === BackendTransaction::TRAN_TYPE_AUTH
                            ) {
                                $saleTransaction->setStatus(Transaction::STATUS_FAILED);
                                $historyTransaction = $saleTransaction;
                            }

                            $transaction->setDataCell('authorizationStatus', $status, 'Current status of the authorization');

                            $result = true;
                            break;
                    }
                }
                break;
            case 'PaymentCapture':
                $captureDetails = isset($ipnData['CaptureDetails'])
                    ? $ipnData['CaptureDetails']
                    : '';

                if ($captureDetails) {
                    $status          = $captureDetails['CaptureStatus']['State'];
                    $amazonCaptureId = $captureDetails['AmazonCaptureId'];

                    $captureTransaction = null;
                    $saleTransaction    = null;

                    if ($this->getSetting('capture_mode') === 'A') {
                        /** @var BackendTransaction $backendTransaction */
                        foreach ($transaction->getBackendTransactions() as $backendTransaction) {
                            if ($backendTransaction->getType() === BackendTransaction::TRAN_TYPE_CAPTURE
                                && ($backendTransaction->getStatus() === BackendTransaction::STATUS_PENDING
                                    || $backendTransaction->getStatus() === BackendTransaction::STATUS_SUCCESS)
                            ) {
                                $captureTransaction = $backendTransaction;
                                break;
                            }
                        }
                    } else {
                        /** @var BackendTransaction $backendTransaction */
                        foreach ($transaction->getBackendTransactions() as $backendTransaction) {
                            if ($backendTransaction->getType() === BackendTransaction::TRAN_TYPE_SALE
                                && ($backendTransaction->getStatus() === BackendTransaction::STATUS_PENDING
                                    || $backendTransaction->getStatus() === BackendTransaction::STATUS_INPROGRESS)
                            ) {
                                $saleTransaction = $backendTransaction;
                                break;
                            }
                        }
                    }

                    switch ($status) {
                        case 'Completed':
                            if ($this->getSetting('capture_mode') === 'A') {
                                if (!$captureTransaction) {
                                    $captureTransaction = $transaction->createBackendTransaction(BackendTransaction::TRAN_TYPE_CAPTURE);
                                    $transaction->setDataCell('amazonCaptureId', $amazonCaptureId, 'AmazonCaptureId identifier');
                                }

                                $captureTransaction->setStatus(BackendTransaction::STATUS_SUCCESS);
                                $historyTransaction = $captureTransaction;

                            } elseif ($saleTransaction) {
                                $transaction->setDataCell('amazonCaptureId', $amazonCaptureId, 'AmazonCaptureId identifier');

                                $saleTransaction->setStatus(BackendTransaction::STATUS_SUCCESS);
                                $historyTransaction = $saleTransaction;
                            }

                            $transaction->setStatus(BackendTransaction::STATUS_SUCCESS);
                            $transaction->setDataCell('authorizationStatus', $status, 'Current status of the authorization');

                            $result = true;
                            break;
                        case 'Declined':
                            if ($this->getSetting('capture_mode') === 'A') {
                                if (!$captureTransaction) {
                                    $captureTransaction = $transaction->createBackendTransaction(BackendTransaction::TRAN_TYPE_CAPTURE);
                                    $transaction->setDataCell('amazonCaptureId', $amazonCaptureId, 'AmazonCaptureId identifier');
                                }

                                $captureTransaction->setStatus(BackendTransaction::STATUS_FAILED);
                                $historyTransaction = $captureTransaction;

                            } elseif ($saleTransaction) {
                                $saleTransaction->setStatus(BackendTransaction::STATUS_FAILED);
                                $historyTransaction = $saleTransaction;
                            }

                            $transaction->setStatus(BackendTransaction::STATUS_FAILED);
                            $transaction->setDataCell('authorizationStatus', $status, 'Current status of the authorization');

                            $result = true;
                            break;
                    }
                }
                break;
            case 'PaymentRefund':
                $refundDetails = isset($ipnData['RefundDetails'])
                    ? $ipnData['RefundDetails']
                    : '';

                if ($refundDetails) {
                    $status         = $refundDetails['RefundStatus']['State'];
                    $amazonRefundId = $refundDetails['AmazonRefundId'];

                    $refundTransaction = null;
                    /** @var BackendTransaction $backendTransaction */
                    foreach ($transaction->getBackendTransactions() as $backendTransaction) {
                        if ($backendTransaction->getType() === BackendTransaction::TRAN_TYPE_REFUND
                            && ($backendTransaction->getStatus() === BackendTransaction::STATUS_PENDING
                                || $backendTransaction->getStatus() === BackendTransaction::STATUS_SUCCESS)
                        ) {
                            $refundTransaction = $backendTransaction;
                            break;
                        }
                    }

                    switch ($status) {
                        case 'Completed':
                            if (!$refundTransaction) {
                                $refundTransaction = $transaction->createBackendTransaction(BackendTransaction::TRAN_TYPE_REFUND);
                                $transaction->setDataCell('amazonRefundId', $amazonRefundId, 'AmazonRefundId identifier');
                            }

                            $refundTransaction->setStatus(BackendTransaction::STATUS_SUCCESS);
                            $transaction->setStatus(BackendTransaction::STATUS_SUCCESS);

                            $transaction->setDataCell('refundStatus', $status, 'Current status of the refund');

                            $historyTransaction = $refundTransaction;
                            $result             = true;
                            break;
                        case 'Pending':
                            if (!$refundTransaction) {
                                $refundTransaction = $transaction->createBackendTransaction(BackendTransaction::TRAN_TYPE_REFUND);
                                $transaction->setDataCell('amazonRefundId', $amazonRefundId, 'AmazonRefundId identifier');
                            }

                            $refundTransaction->setStatus(BackendTransaction::STATUS_PENDING);

                            $historyTransaction = $refundTransaction;
                            $result             = true;
                            break;
                    }
                }

                break;
        }

        if ($result) {
            if ($historyTransaction) {
                $historyTransaction->registerTransactionInOrderHistory('Callback IPN');

            } else {
                $transaction->registerTransactionInOrderHistory('Callback IPN');
            }

            \XLite\Core\Database::getEM()->flush();
        }
    }

    /**
     * Define saved into transaction data schema
     *
     * @return array
     */
    protected function defineSavedData()
    {
        $data = parent::defineSavedData();

        $data['amazonOrderReferenceId'] = 'Amazon order reference ID';
        $data['authorizationStatus']    = 'Current status of the authorization';
        $data['authorizationReason']    = 'Current reason of the authorization';
        $data['amazonAuthorizationId']  = 'The Amazon-generated identifier for this authorization transaction';
        $data['amazonCaptureId']        = 'AmazonCaptureId identifier';

        return $data;
    }

    /**
     * @param string $request
     * @param array  $data
     *
     * @return array
     * @throws APIException
     */
    protected function clientRequest($request, $data)
    {
        $result = [];
        $client = Main::getClient();

        if (method_exists($client, $request)) {
            /** @var \PayWithAmazon\ResponseParser $response */
            $response = $client->$request($data);

            Main::log(
                [
                    'message'     => __FUNCTION__,
                    'request'     => $request,
                    'data'        => $data,
                    'response'    => $response->toArray(),
                    'rawResponse' => $response->toXml(),
                ]
            );

            $result = $response->toArray();
            if (isset($result['Error']['Message'])) {
                throw new APIException($result['Error']['Message']);
            }
        } else {
            Main::log(
                [
                    'message' => 'Error: ' . __FUNCTION__ . ' (Wrong request)',
                    'request' => $request,
                    'data'    => $data,
                ]
            );
        }

        return $result;
    }
}
