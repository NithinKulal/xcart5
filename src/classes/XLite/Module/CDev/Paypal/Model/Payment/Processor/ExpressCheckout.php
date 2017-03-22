<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\Model\Payment\Processor;

/**
 * Paypal Express Checkout payment processor
 */
class ExpressCheckout extends \XLite\Module\CDev\Paypal\Model\Payment\Processor\APaypal
{
    /**
     * Express Checkout flow types definition
     */
    const EC_TYPE_SHORTCUT = 'shortcut';
    const EC_TYPE_MARK     = 'mark';

    /**
     * Express Checkout token TTL is 3 hours (10800 seconds)
     */
    const TOKEN_TTL = 10800;

    /**
     * Maximum tries to checkout when getting 10486 error
     */
    const MAX_TRIES = 3;

    /**
     * Referral page URL
     *
     * @var string
     */
    protected $referralPageURL = 'https://www.paypal.com/webapps/mpp/referral/paypal-express-checkout?partner_id=';

    /**
     * Knowledge base page URL
     *
     * @var string
     */
    protected $knowledgeBasePageURL = 'http://kb.x-cart.com/en/payments/paypal/setting_up_paypal_express_checkout.html';

    /**
     * Error message
     *
     * @var string
     */
    protected $errorMessage = null;

    // {{{ Common

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        $method = \XLite\Module\CDev\Paypal\Main::getPaymentMethod(
            \XLite\Module\CDev\Paypal\Main::PP_METHOD_EC
        );

        $this->api->setMethod($method);
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
        return 'modules/CDev/Paypal/checkout/expressCheckout.twig';
    }

    /**
     * Get input template
     *
     * @return string|void
     */
    public function getInputTemplate()
    {
        return 'modules/CDev/Paypal/checkout/in_context_checkout.twig';
    }

    /**
     * Get the list of merchant countries where this payment processor can work
     *
     * @return array
     */
    public function getAllowedMerchantCountries()
    {
        return ['US', 'CA', 'AU', 'NZ'];
    }

    /**
     * Returns last error message
     *
     * @return string
     */
    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

    /**
     * Is In-Context Boarding SignUp available
     *
     * @return boolean
     */
    public function isInContextSignUpAvailable()
    {
        $api = \XLite\Module\CDev\Paypal\Main::getRESTAPIInstance();

        return $api->isInContextSignUpAvailable();
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
        $api = \XLite\Module\CDev\Paypal\Main::getRESTAPIInstance();
        $controller = \XLite::getController();

        if ($api->isInContextSignUpAvailable()) {
            $returnUrl = $controller->getShopURL(
                $controller->buildURL('paypal_settings', 'update_credentials')
            );
            $url = $api->getSignUpUrl($returnUrl);

        } else {
            $url = parent::getReferralPageURL($method);
        }

        return $url;
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
        if (static::EC_TYPE_MARK == \XLite\Core\Session::getInstance()->ec_type) {
            $url = $this->getReturnURL(null, true);

        } else {
            $url = \XLite::getInstance()->getShopURL(
                \XLite\Core\Converter::buildURL('checkout', 'express_checkout_return'),
                \XLite\Core\Config::getInstance()->Security->customer_security
            );
        }

        return $url;
    }

    /**
     * Returns payment cancel url
     *
     * @return string
     */
    public function getPaymentCancelUrl()
    {
        if (static::EC_TYPE_MARK == \XLite\Core\Session::getInstance()->ec_type) {
            $url = $this->getReturnURL(null, true, true);

        } else {
            $url = \XLite::getInstance()->getShopURL(
                \XLite\Core\Converter::buildURL('checkout', 'express_checkout_return', ['cancel' => 1]),
                \XLite\Core\Config::getInstance()->Security->customer_security
            );
        }

        if (\XLite\Core\Request::getInstance()->cancelUrl) {
            $url .= '&cancelUrl=' . urlencode(\XLite\Core\Request::getInstance()->cancelUrl);
        }

        return $url;
    }

    // }}}

    // {{{ Payment process

    /**
     * Process return (this used when customer pay via Express Checkout mark flow)
     *
     * @param \XLite\Model\Payment\Transaction $transaction Payment transaction object
     *
     * @return void
     */
    public function processReturn(\XLite\Model\Payment\Transaction $transaction)
    {
        parent::processReturn($transaction);

        if (!\XLite\Core\Request::getInstance()->cancel) {
            \XLite\Core\Session::getInstance()->ec_payer_id = \XLite\Core\Request::getInstance()->PayerID;
            $this->doDoExpressCheckoutPayment();
        }
    }

    /**
     * Do initial payment and return status
     *
     * @return string
     */
    protected function doInitialPayment()
    {
        $this->transaction->createBackendTransaction($this->getInitialTransactionType());

        $result = self::FAILED;

        if (!$this->isTokenValid() || self::EC_TYPE_MARK == \XLite\Core\Session::getInstance()->ec_type) {
            \XLite\Core\Session::getInstance()->ec_type = self::EC_TYPE_MARK;

            $token = $this->doSetExpressCheckout($this->transaction->getPaymentMethod());

            if (isset($token)) {
                \XLite\Core\Session::getInstance()->ec_token = $token;
                \XLite\Core\Session::getInstance()->ec_date = \XLite\Core\Converter::time();
                \XLite\Core\Session::getInstance()->ec_payer_id = null;

                $result = static::PROLONGATION;

                $this->redirectToPaypal($token);

                if (self::EC_TYPE_MARK !== \XLite\Core\Session::getInstance()->ec_type) {
                    exit ();
                }

            } else {
                $this->transaction->setDataCell(
                    'status',
                    $this->errorMessage ?: 'Failure to redirect to PayPal.',
                    null,
                    'C'
                );
            }

        } else {
            $result = $this->doDoExpressCheckoutPayment();
        }

        return $result;
    }

    // }}}

    // {{{ Redirect to Paypal

    /**
     * Redirect customer to Paypal server for authorization and address selection
     *
     * @param string $token Express Checkout token
     *
     * @return void
     */
    public function redirectToPaypal($token)
    {
        $url = $this->getRedirectURL($this->getPostParams($token));

        \XLite\Module\CDev\Paypal\Main::addLog(
            'redirectToPaypal()',
            $url
        );

        $page = <<<HTML
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" dir="ltr">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body onload="self.location = '$url';">
</body>
</html>
HTML;

        print ($page);
    }

    /**
     * Get express checkout redirect url
     *
     * @param string $token Express Checkout token
     *
     * @return array
     */
    public function getExpressCheckoutRedirectURL($token)
    {
        return $this->getRedirectURL($this->getPostParams($token));
    }

    /**
     * Get post URL
     * todo: use http_build_query
     *
     * @param array $params Array of URL parameters OPTIONAL
     *
     * @return string
     */
    public function getRedirectURL($params = [])
    {
        $postURL = $this->getExpressCheckoutPostURL();

        $postData = [];

        foreach ($params as $k => $v) {
            $postData[] = sprintf('%s=%s', $k, $v);
        }

        return $postURL . '?' . implode('&', $postData);
    }

    /**
     * Get array of parameters for redirecting customer to Paypal server
     *
     * @param string $token Express Checkout token
     *
     * @return array
     */
    public function getPostParams($token)
    {
        $params = [
            'token' => $token,
        ];

        if (!\XLite\Core\Request::getInstance()->inContext) {
            $params['cmd'] = '_express-checkout';
        }

        if (\XLite\Core\Session::getInstance()->ec_ignore_checkout) {
            $params['useraction'] = 'commit';
        }

        return $params;
    }

    /**
     * Get PostURL to redirect customer to Paypal
     *
     * @return string
     */
    protected function getExpressCheckoutPostURL()
    {
        $testMode = $this->isTestMode($this->transaction->getPaymentMethod());
        $inContext = \XLite\Core\Request::getInstance()->inContext;


        if ($inContext) {
            $result = $testMode
                ? 'https://www.sandbox.paypal.com/checkoutnow'
                : 'https://www.paypal.com/checkoutnow';
        } else {
            $result = $testMode
                ? 'https://www.sandbox.paypal.com/cgi-bin/webscr'
                : 'https://www.paypal.com/cgi-bin/webscr';
        }

        return $result;
    }

    /**
     * Retry ExpressCheckout
     *
     * @param string $token Express Checkout token value
     *
     * @return void
     */
    protected function retryExpressCheckout($token)
    {
        \XLite\Core\Session::getInstance()->expressCheckoutRetry
            = (\XLite\Core\Session::getInstance()->expressCheckoutRetry ?: 0) + 1;

        $this->redirectToPaypal($token);

        exit ();
    }

    /**
     * Is retryExpressCheckout allowed
     *
     * @return boolean
     */
    protected function isRetryExpressCheckoutAllowed()
    {
        $result = is_null(\XLite\Core\Session::getInstance()->expressCheckoutRetry)
            || \XLite\Core\Session::getInstance()->expressCheckoutRetry < static::MAX_TRIES;

        if (!$result) {
            \XLite\Core\Session::getInstance()->expressCheckoutRetry = 0;
        }

        return $result;
    }

    // }}}

    // {{{ Merchant id

    /**
     * Returns merchant id
     *
     * @return mixed
     */
    public function retrieveMerchantId()
    {
        return $this->api->getMerchantID();
    }

    // }}}

    // {{{ SetExpressCheckout

    /**
     * Perform 'SetExpressCheckout' request and get Token value from Paypal
     *
     * @param \XLite\Model\Payment\Method $method Payment method
     *
     * @return string
     */
    public function doSetExpressCheckout(\XLite\Model\Payment\Method $method)
    {
        $token = null;

        if (!isset($this->transaction)) {
            $this->transaction = new \XLite\Model\Payment\Transaction();
            $this->transaction->setPaymentMethod($method);
            $this->transaction->setOrder(\XLite\Model\Cart::getInstance());
        }

        $responseData = $this->doRequest('SetExpressCheckout');

        if (!empty($responseData['TOKEN'])) {
            $token = $responseData['TOKEN'];

        } else {
            $this->setDetail(
                'status',
                isset($responseData['RESPMSG']) ? $responseData['RESPMSG'] : 'Unknown',
                'Status'
            );

            $transaction = \XLite\Model\Cart::getInstance()->getFirstOpenPaymentTransaction();
            if ($transaction) {
                $this->processFailTryPayment($transaction);
            }

            $this->errorMessage = isset($responseData['RESPMSG']) ? $responseData['RESPMSG'] : null;
        }

        return $token;
    }

    /**
     * Get array of parameters for SET_EXPRESS_CHECKOUT request
     *
     * @return array
     */
    protected function getSetExpressCheckoutRequestParams()
    {
        $params = $this->api->convertSetExpressCheckoutParams($this->getOrder());

        $orderNumber = $this->getTransactionId($this->getSetting('prefix'));
        $params['INVNUM'] = $orderNumber;
        $params['CUSTOM'] = $orderNumber;

        return $params;
    }

    // }}}

    // {{{ GetExpressCheckoutDetails

    /**
     * doGetExpressCheckoutDetails
     *
     * @param \XLite\Model\Payment\Method $method Payment method object
     *
     * @return array
     */
    public function doGetExpressCheckoutDetails(\XLite\Model\Payment\Method $method)
    {
        $data = [];

        if (!isset($this->transaction)) {
            $this->transaction = new \XLite\Model\Payment\Transaction();
            $this->transaction->setPaymentMethod($method);
        }

        $responseData = $this->doRequest('GetExpressCheckoutDetails');

        if (!empty($responseData) && '0' == $responseData['RESULT']) {
            $data = $responseData;
        }

        return $data;
    }

    /**
     * Return array of parameters for 'GetExpressCheckoutDetails' request
     *
     * @return array
     */
    protected function getGetExpressCheckoutDetailsRequestParams()
    {
        $token = \XLite\Core\Session::getInstance()->ec_token;

        return $this->api->convertGetExpressCheckoutDetailsParams($token);
    }

    // }}}

    // {{{ DoExpressCheckoutPayment

    /**
     * Perform 'DoExpressCheckoutPayment' request and return status of payment transaction
     *
     * @return string
     */
    protected function doDoExpressCheckoutPayment()
    {
        $status = self::FAILED;

        $transaction = $this->transaction;

        $responseData = $this->doRequest(
            'DoExpressCheckoutPayment',
            $transaction->getInitialBackendTransaction()
        );

        $transactionStatus = $transaction::STATUS_FAILED;

        if (!empty($responseData)) {
            if ('0' == $responseData['RESULT']) {
                if ($this->isSuccessResponse($responseData)) {
                    $transactionStatus = $transaction::STATUS_SUCCESS;
                    $status = self::COMPLETED;

                } else {
                    $transactionStatus = $transaction::STATUS_PENDING;
                    $status = self::PENDING;
                }

            } elseif ((preg_match('/^Generic processor error: 10486/', $responseData['RESPMSG'])
                || preg_match('/^10486/', $responseData['RESPMSG'])
                )
                && $this->isRetryExpressCheckoutAllowed()
            ) {
                $this->retryExpressCheckout(\XLite\Core\Session::getInstance()->ec_token);

            } else {
                $this->setDetail(
                    'status',
                    'Failed: ' . $responseData['RESPMSG'],
                    'Status'
                );
            }

            // Save payment transaction data
            $this->saveFilteredData($responseData);

        } else {
            $this->setDetail(
                'status',
                'Failed: unexpected response received from PayPal',
                'Status'
            );
        }

        $transaction->setStatus($transactionStatus);

        $this->updateInitialBackendTransaction($transaction, $transactionStatus);

        \XLite\Core\Session::getInstance()->ec_token = null;
        \XLite\Core\Session::getInstance()->ec_date = null;
        \XLite\Core\Session::getInstance()->ec_payer_id = null;
        \XLite\Core\Session::getInstance()->ec_type = null;

        return $status;
    }

    /**
     * Return array of parameters for 'DoExpressCheckoutPayment' request
     *
     * @return array
     */
    protected function getDoExpressCheckoutPaymentRequestParams()
    {
        $transaction = $this->transaction;
        $token = \XLite\Core\Session::getInstance()->ec_token;
        $payerId = \XLite\Core\Session::getInstance()->ec_payer_id;

        $params = $this->api->convertDoExpressCheckoutPaymentParams($transaction, $token, $payerId);

        $orderNumber = $this->getTransactionId($this->getSetting('prefix'));
        $params['INVNUM'] = $orderNumber;
        $params['CUSTOM'] = $orderNumber;

        return $params;
    }

    /**
     * Return true if Paypal response is a success transaction response
     *
     * @param array $response Response data
     *
     * @return boolean
     */
    protected function isSuccessResponse($response)
    {
        $result = in_array(strtolower($response['PENDINGREASON']), ['none', 'completed']);

        if (!$result) {
            $result = (
                'authorization' == $response['PENDINGREASON']
                && \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_AUTH == $this->transaction->getType()
            );
        }

        return $result;
    }

    // }}}

    // {{{ Additional methods

    /**
     * Get return type
     *
     * @return string
     */
    public function getReturnType()
    {
        return \XLite\Model\Payment\Base\Online::RETURN_TYPE_HTTP_REDIRECT;
    }

    /**
     * Translate array of data received from Paypal to the array for updating cart
     * todo: mode to api
     *
     * @param array $paypalData Array of customer data received from Paypal
     *
     * @return array
     */
    public function prepareBuyerData($paypalData)
    {
        $countryCode = \Includes\Utils\ArrayManager::getIndex($paypalData, 'SHIPTOCOUNTRY', true);
        $country = \XLite\Core\Database::getRepo('XLite\Model\Country')
            ->findOneByCode($countryCode);

        $stateCode = \Includes\Utils\ArrayManager::getIndex($paypalData, 'SHIPTOSTATE', true);
        $state = ($country && $stateCode)
            ? \XLite\Core\Database::getRepo('XLite\Model\State')
                ->findOneByCountryAndState($country->getCode(), $stateCode)
            : null;

        $data = [
            'shippingAddress' => [
                'name' => $paypalData['SHIPTONAME'],
                'street' => $paypalData['SHIPTOSTREET'] . (!empty($paypalData['SHIPTOSTREET2']) ? ' ' . $paypalData['SHIPTOSTREET2'] : ''),
                'country' => $country ?: '',
                'state' => $state ? $state : $paypalData['SHIPTOSTATE'],
                'city' => $paypalData['SHIPTOCITY'],
                'zipcode' => $paypalData['SHIPTOZIP'],
                'phone' => isset($paypalData['PHONENUM']) ? $paypalData['PHONENUM'] : '',
            ],
        ];

        return $data;
    }

    /**
     * Returns true if token initialized and is not expired
     *
     * @return boolean
     */
    protected function isTokenValid()
    {
        return !empty(\XLite\Core\Session::getInstance()->ec_token)
            && self::TOKEN_TTL > \XLite\Core\Converter::time() - \XLite\Core\Session::getInstance()->ec_date;
    }

    /**
     * Get allowed currencies
     * https://www.paypalobjects.com/webstatic/en_US/developer/docs/pdf/pfp_expresscheckout_pp.pdf
     *
     * @param \XLite\Model\Payment\Method $method Payment method
     *
     * @return array
     */
    protected function getAllowedCurrencies(\XLite\Model\Payment\Method $method)
    {
        return array_merge(
            parent::getAllowedCurrencies($method),
            [
                'USD', 'CAD', 'EUR', 'GBP', 'AUD',
                'CHF', 'JPY', 'NOK', 'NZD', 'PLN',
                'SEK', 'SGD', 'HKD', 'DKK', 'HUF',
                'CZK', 'BRL', 'ILS', 'MYR', 'MXN',
                'PHP', 'TWD', 'THB',
            ]
        );
    }

    // }}}
}
