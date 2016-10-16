<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\Core;

use \XLite\Module\CDev\Paypal;

/**
 * Paypal adaptive API
 */
class PaypalAdaptiveAPI extends \XLite\Module\CDev\Paypal\Core\AAPI
{
    const PAYPAL_ADAPTIVE_MAX_RECEIVERS = 9;

    /**
     * PartnerCode aka referrerCode
     * @var string
     */
    protected $partnerCode;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->method = \XLite\Module\CDev\Paypal\Main::getPaymentMethod(
            \XLite\Module\CDev\Paypal\Main::PP_METHOD_PAD
        );
    }

    /**
     * Set partner code
     * 
     * @param string $partnerCode PartnerCode aka referrerCode
     * 
     * @return void
     */
    public function setPartnerCode($partnerCode)
    {
        $this->partnerCode = $partnerCode;
    }

    // {{{ Configuration

    /**
     * Check - payment method is configured or not
     *
     * @return boolean
     */
    public function isConfigured()
    {
        return $this->getSetting('app_id')
            && $this->getSetting('api_username')
            && $this->getSetting('api_password')
            && $this->getSetting('signature')
            && $this->getSetting('paypal_login');
    }

    /**
     * Get all possible receivers of chained payment
     * If receiver doesn't have valid paypal account
     * or count of receivers exceeded possible amount
     * these receiver will not get automate commission payment
     * 
     * @param \XLite\Model\Order $order Order for generation receivers
     * 
     * @return array Associative array of receivers allowed for automatic commission payment
     */
    protected function getReceivers(\XLite\Model\Order $order)
    {
        $receivers = array();

        $receivers[0] = array(
            'amount'    => $order->getTotal(),
            'email'     => $this->getSetting('paypal_login'),
        );

        if ($this->isChained($order)) {
            $receivers = array_merge(
                $receivers,
                $this->getSecondaryReceivers($order)
            );
        }

        $receivers = array_filter($receivers);
        if ($this->isChained($order) && 1 < count($receivers)) {
            $receivers[0]['primary'] = true;
        }

        return $receivers;
    }

    /**
     * Get secondary receivers
     * 
     * @param \XLite\Model\Order $order Order
     * 
     * @return array
     */
    protected function getSecondaryReceivers(\XLite\Model\Order $order)
    {
        return array();
    }

    /**
     * Get 'feesPayer' value for PAY api call
     * 
     * @param \XLite\Model\Order $order Order
     * 
     * @return string 'feesPayer' value for PAY api call
     */
    protected function getFeePayerType(\XLite\Model\Order $order, $validReceiversCount)
    {
        $defaultFeesPayer = 'EACHRECEIVER';

        $isChained = $this->isChained($order) && 1 <= $validReceiversCount;

        $processedFeesPayer = $this->getSetting('feesPayer');

        if ($isChained) {
            if ('SENDER' === $processedFeesPayer) {
                $processedFeesPayer = $defaultFeesPayer;

            } elseif ('SECONDARYONLY' === $processedFeesPayer && 1 !== $validReceiversCount) {
                $processedFeesPayer = $defaultFeesPayer;
            }

        } elseif ('PRIMARYRECEIVER' === $processedFeesPayer || 'SECONDARYONLY' === $processedFeesPayer) {
            $processedFeesPayer = $defaultFeesPayer;
        }

        return $processedFeesPayer;
    }

    /**
     * Check if transaction should be chained of not
     * 
     * @param \XLite\Model\Order $order Order
     * 
     * @return boolean
     */
    protected function isChained(\XLite\Model\Order $order)
    {
        return false;
    }

    /**
     * Do PAY api request
     * 
     * @param \XLite\Model\Order    $order                  Order
     * @param string                $cancelURL              User will be redirected to this URL if cancel his payment
     * @param string                $returnURL              User will be redirected to this URL in case of successful payment
     * @param string                $ipnNotificationUrl     URL to receive IPN notifications
     * 
     * @return array Paypal server response to PAY api call
     */
    public function doPayCall(\XLite\Model\Order $order, $cancelURL, $returnURL, $ipnNotificationUrl)
    {
        $receivers = array_values($this->getReceivers($order));

        $currencyCode = $order->getCurrency()
            ? $order->getCurrency()->getCode()
            : 'USD';

        $params = array(
            'actionType'    => 'CREATE',
            'clientDetails' => array(
                'partnerName' => $this->partnerCode
            ),
            'currencyCode'  => $currencyCode,
            'feesPayer'     => $this->getFeePayerType($order, count($receivers)),
            'receiverList'  => array(
                'receiver'  => $receivers
            ),
            'cancelUrl'             => $cancelURL,
            'returnUrl'             => $returnURL,
            'ipnNotificationUrl'    => $ipnNotificationUrl,
        );

        return $this->call(
            'Pay',
            $params
        );
    }

    /**
     * Do SetPaymentOptions api request
     *
     * @param string $payKey Pay key, should be received from PAY call
     *
     * @return array Paypal server response to SetPaymentOptions api call
     */
    public function doSetPaymentOptionsCall($payKey)
    {
        $params = array(
            'payKey'        => $payKey,
            'senderOptions' => array(
                'referrerCode' => $this->partnerCode
            ),
        );

        return $this->call(
            'SetPaymentOptions',
            $params
        );
    }

    /**
     * Do ExecutePayment api request
     *
     * @param string $payKey Pay key, should be received from PAY call
     *
     * @return array Paypal server response to ExecutePayment api call
     */
    public function doExecutePaymentCall($payKey)
    {
        $params = array(
            'payKey'        => $payKey,
        );

        return $this->call(
            'ExecutePayment',
            $params
        );
    }

    /**
     * Get match criteria value
     *
     * @return string
     */
    public function getMatchCriteria()
    {
        return $this->getSetting('matchCriteria');
    }

    /**
     * Do GetVerifiedStatus api request
     * 
     * @param string $paypalLogin Paypal login email
     * 
     * @return array Paypal server response to GetVerifiedStatus api call
     */
    public function doGetVerifiedStatusCall($paypalLogin)
    {
        $params = array(
            'emailAddress'  => $paypalLogin,
            'matchCriteria' => 'NONE'
        );

        return $this->call(
            'GetVerifiedStatus',
            $params
        );
    }

    /**
     * Do GetVerifiedStatus api request
     *
     * @param string $paypalLogin Paypal login email
     *
     * @return array Paypal server response to GetVerifiedStatus api call
     */
    public function doGetVerifiedStatusCallWithCriteria($matchCriteria, array $params)
    {
        if ($matchCriteria === 'name') {
            $params = array(
                'emailAddress'  => $params['paypalLogin'],
                'firstName'     => $params['firstName'],
                'lastName'      => $params['lastName'],
                'matchCriteria' => 'NAME'
            );
        } elseif ($matchCriteria === 'none') {
            $params = array(
                'emailAddress'  => $params['paypalLogin'],
                'matchCriteria' => 'NONE'
            );
        } else {
            return null;
        }

        return $this->call(
            'GetVerifiedStatus',
            $params
        );
    }


    /**
     * Do paypal api request
     * 
     * @param string    $method     Paypal API operation name
     * @param array     $options    Array of parameters to request
     * 
     * @return array Paypal API server response
     */
    public function call($method, $options = array())
    {
        $this->prepare($options);

        return $this->curl($this->getApiURL($method), $options, $this->headers());
    }

    /**
     * Get API operation url by its name and testmode
     * 
     * @param string Paypal API operation name
     * 
     * @return string Paypal Api operation url
     */
    protected function getApiURL($method)
    {
        $url = $this->isTestMode()
            ? 'https://svcs.sandbox.paypal.com/'
            : 'https://svcs.paypal.com/';

        switch ($method) {
            case 'Pay':
                $url .= 'AdaptivePayments/Pay';
                break;

            case 'GetVerifiedStatus':
                $url .= 'AdaptiveAccounts/GetVerifiedStatus';
                break;

            case 'SetPaymentOptions':
                $url .= 'AdaptivePayments/SetPaymentOptions';
                break;

            case 'ExecutePayment':
                $url .= 'AdaptivePayments/ExecutePayment';
                break;

            default:
                $url .= 'AdaptivePayments/Pay';
                break;
        }
        return $url;
    }

    /**
     * Get headers for paypal api request
     * 
     * @return Array of headers for paypal api request
     */
    protected function headers()
    {
        $header = array(
            'X-PAYPAL-SECURITY-USERID: ' . $this->getSetting('api_username'),
            'X-PAYPAL-SECURITY-PASSWORD: ' . $this->getSetting('api_password'),
            'X-PAYPAL-SECURITY-SIGNATURE: ' . $this->getSetting('signature'),
            'X-PAYPAL-REQUEST-DATA-FORMAT: JSON',
            'X-PAYPAL-RESPONSE-DATA-FORMAT: JSON',
        );

        $header[] = 'X-PAYPAL-APPLICATION-ID: ' . $this->getSetting('app_id');

        return $header;
    }

    /**
     * Do CURL request
     * 
     * @param string    $url        URL of request
     * @param array     $values     Parameters of request
     * @param array     $header     Headers of request
     * 
     * @return array Server response
     */
    protected function curl($url, $values, $header)
    {
        $curl = curl_init($url);

        $options = array(
            CURLOPT_HTTPHEADER     => $header,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_POSTFIELDS     => json_encode($values),
            CURLOPT_CUSTOMREQUEST  => 'POST',
            CURLOPT_TIMEOUT        => 10
        );

        curl_setopt_array($curl, $options);
        $rep = curl_exec($curl);

        $response = json_decode($rep, true);
        if (isset($response['error']) && $response['error']) {
            Paypal\Main::addLog(
                'Adaptive payments error',
                $response['error']
            );
        }
        curl_close($curl);

        return $response;
    }

    /**
     * Preparing request parameters
     * 
     * @param array $options Request parameters
     * 
     * @return void
     */
    protected function prepare(&$options)
    {
        $this->doExpandURLs($options);
        $this->doMergeDefaults($options);
    }

    /**
     * Expand urls
     * 
     * @param array $options Request parameters
     * 
     * @return void
     */
    protected function doExpandURLs(&$options)
    {
        $regex = '#^https?://#i';
        if (array_key_exists('returnUrl', $options) && !preg_match($regex, $options['returnUrl'])) {
            $this->doExpandURL($options['returnUrl']);
        }

        if (array_key_exists('cancelUrl', $options) && !preg_match($regex, $options['cancelUrl'])) {
            $this->doExpandURL($options['cancelUrl']);
        }
    }

    /**
     * Expand url
     * 
     * @param string
     * 
     * @return void
     */
    protected function doExpandURL(&$url)
    {
        $currentHost = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'];

        if (preg_match("#^/#i", $url)) {
            $url = $currentHost . $url;
        } else {
            $directory = dirname($_SERVER['PHP_SELF']);
            $url = $currentHost . $directory . $url;
        }
    }

    /**
     * Merge defaults
     * 
     * @param array $options Request parameters
     * 
     * @return void
     */
    protected function doMergeDefaults(&$options)
    {
        $defaults = array(
            'requestEnvelope' => array(
                'errorLanguage' => 'en_US'
            )
        );

        $options = array_merge($defaults, $options);
    }
}
