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

namespace XLite\Module\CDev\XPaymentsConnector\Core;

/**
 * XPayments client
 */
class XPaymentsClient extends \XLite\Base\Singleton
{
    const REQ_CURL    = 1;
    const REQ_OPENSSL = 2;
    const REQ_DOM     = 4;

    const XPC_SYSERR_CARTID      = 1;
    const XPC_SYSERR_URL         = 2;
    const XPC_SYSERR_PUBKEY      = 4;
    const XPC_SYSERR_PRIVKEY     = 8;
    const XPC_SYSERR_PRIVKEYPASS = 16;

    /**
     * Show or not save card checkbox statuses
     */
    const SAVE_CARD_DISABLED = 'N';
    const SAVE_CARD_REQUIRED = 'Y';
    const SAVE_CARD_OPTIONAL = 'O';

    /**
     * Log file names
     */
    const LOG_FILE       = 'xp-connector';
    const LOG_FILE_ERROR = 'xp-connector-error';

    /**
     * Api request object
     */
    protected $apiRequest = null;

    /**
     * Decrypted and processed update data
     *
     * @var array
     */
    protected static $updateData = false;

    /**
     * Check - module is configured or not
     *
     * @return boolean
     */
    public function isModuleConfigured()
    {
        return 0 === $this->getModuleSystemErrors();
    }

    /**
     * Make test request to X-Payments
     *
     * @param string $apiVersion API version, overrides configuration value OPTIONAL
     *
     * @return boolean
     */
    public function requestTest($apiVersion = false)
    {
        $hashCode = strval(rand(0, 1000000));

        // Make test request
        $test = $this->apiRequest->send(
            'connect',
            'test',
            array('testCode' => $hashCode),
            $apiVersion
        );

        // Compare MD5 hashes
        if ($test->isSuccess()) {

            $response = $test->getResponse();

            if (md5($hashCode) !== $response['hashCode']) {
                $test->setError('Test connection data is not valid');
            }
        }

        return $test;
    }

    /**
     * Get payment info
     *
     * @param integer $txnId   Transaction id
     * @param boolean $refresh Refresh OPTIONAL
     *
     * @return array Operation status & payment data array
     */
    public function requestPaymentInfo($txnId, $refresh = false)
    {
        $data = array(
            'txnId'   => $txnId,
            'refresh' => $refresh ? 1 : 0
        );

        return $this->apiRequest->send('payment', 'get_info', $data);
    }

    /**
     * Get payment info
     *
     * @param integer $txnId   Transaction id
     *
     * @return array Operation status & payment data array
     */
    public function requestPaymentAdditionalInfo($txnId)
    {
        $data = array(
            'txnId'   => $txnId,
        );

        return $this->apiRequest->send('payment', 'get_additional_info', $data);
    }

    /**
     * Get list of available payment configurations from X-Payments
     *
     * @return array
     */
    public function requestPaymentMethods()
    {
        $result = array();

        $confs = $this->apiRequest->send('payment_confs', 'get');

        if ($confs->isSuccess()) {
           
            $response = $confs->getResponse();
 
            if (
                !empty($response['payment_module'])
                && is_array($response['payment_module'])
            ) {
                $result = $response['payment_module'];
            }
        }

        return $result;
    }

    /**
     * Get recharge request via saved credit card 
     *
     * @param string                           $txnId       Transaction ID
     * @param \XLite\Model\Payment\Transaction $transaction Payment transaction
     * @param string                           $description Description OPTIONAL
     *
     * @return array
     */
    public function requestPaymentRecharge($txnId, \XLite\Model\Payment\Transaction $transaction, $description = null)
    {
        if (!$transaction->getPublicId()) {
            // WA fix for this change: http://xcn.myjetbrains.com/youtrack/issue/BUG-2463
            // Apparently should be removed in next release.
            $transaction->renewTransactionId();
        }

        $xpcBackReference = $transaction->getPublicId();

        // Save back refernece to transaction from  X-Payments
        $transaction->setDataCell('xpcBackReference', $xpcBackReference, 'X-Payments back reference', 'C');
        \XLite\Core\Database::getEM()->flush();

        $paymentMethod = $transaction->getPaymentMethod();

        // Prepare cart
        $preparedCart = $this->prepareCart($transaction->getOrder(), $transaction->getPaymentMethod(), $xpcBackReference);

        return $this->apiRequest->send(
            'payment',
            'recharge',
            array(
                'callbackUrl' => self::getCallbackUrl($xpcBackReference),
                'txnId'       => $txnId,
                'amount'      => $transaction->getValue(),
                'description' => !isset($description) ? 'New payment for transaction #' . $txnId : $description,
                'refId'       => $xpcBackReference,
                'cart'        => $preparedCart, 
            )
        );
    }

    /**
     * Capture request 
     *
     * @param string                           $txnId       Transaction ID
     * @param \XLite\Model\Payment\Transaction $transaction Payment transaction
     * @param int                              $amount      Amount OPTIONAL
     *
     * @return array
     */
    public function requestPaymentCapture($txnId, \XLite\Model\Payment\Transaction $transaction, $amount = null)
    {
        $data = array(
            'txnId' => $txnId,
        );

        if ($amount && is_numeric($amount)) {
            $data['amount'] = $amount;
        } 

        return $this->apiRequest->send('payment', 'capture', $data);
    }

    /**
     * Void request
     *
     * @param string                           $txnId       Transaction ID
     * @param \XLite\Model\Payment\Transaction $transaction Payment transaction
     * @param int                              $amount      Amount OPTIONAL
     *
     * @return array
     */
    public function requestPaymentVoid($txnId, \XLite\Model\Payment\Transaction $transaction, $amount = null)
    {
        $data = array(
            'txnId' => $txnId,
        );

        if ($amount && is_numeric($amount)) {
            $data['amount'] = $amount;
        }

        return $this->apiRequest->send('payment', 'void', $data);
    }

    /**
     * Refund request
     *
     * @param string                           $txnId       Transaction ID
     * @param \XLite\Model\Payment\Transaction $transaction Payment transaction
     * @param int                              $amount      Amount OPTIONAL
     *
     * @return array
     */
    public function requestPaymentRefund($txnId, \XLite\Model\Payment\Transaction $transaction, $amount = null)
    {
        $data = array(
            'txnId' => $txnId,
        );

        if ($amount && is_numeric($amount)) {
            $data['amount'] = $amount;
        }

        return $this->apiRequest->send('payment', 'refund', $data);
    }

    /**
     * Accept request
     *
     * @param string                           $txnId       Transaction ID
     * @param \XLite\Model\Payment\Transaction $transaction Payment transaction
     *
     * @return array
     */
    public function requestPaymentAccept($txnId, \XLite\Model\Payment\Transaction $transaction)
    {
        $data = array(
            'txnId' => $txnId,
        );

        return $this->apiRequest->send('payment', 'accept', $data);
    }

    /**
     * Decline request
     *
     * @param string                           $txnId       Transaction ID
     * @param \XLite\Model\Payment\Transaction $transaction Payment transaction
     *
     * @return array
     */
    public function requestPaymentDecline($txnId, \XLite\Model\Payment\Transaction $transaction)
    {
        $data = array(
            'txnId' => $txnId,
        );

        return $this->apiRequest->send('payment', 'decline', $data);
    }

    /**
     * Clear init payment form data from session
     *
     * @param integer $paymentId Payment id OPTIONAL
     *
     * @return void
     */
    public function clearInitDataFromSession($paymentId = null)
    {
        if (
            $paymentId
            && \XLite\Core\Session::getInstance()->xpc_form_data
            && \XLite\Core\Session::getInstance()->xpc_form_data[$paymentId]
        ) {

            unset(\XLite\Core\Session::getInstance()->xpc_form_data[$paymentId]);

        } else {

            unset(\XLite\Core\Session::getInstance()->xpc_form_data);

        }
    }

    /**
     * Clear init payment form data from all sessions for all payments and users
     *
     * @return void
     */
    public function clearAllInitData()
    {
        $cells = \XLite\Core\Database::getRepo('\XLite\Model\SessionCell')->findByName('xpc_form_data');

        if ($cells) {
            \XLite\Core\Database::getRepo('\XLite\Model\SessionCell')->deleteInBatch($cells);
        }
    }

    /**
     * Check if data is valid for init payment form
     * It should contain form fields and transaction ID
     *
     * For session operations only
     *
     * @param array $data Data
     *
     * @return boolean
     */
    protected function isInitDataValid($data)
    {
        return !empty($data)
            && is_array($data)
            && !empty($data['txnId'])
            && !empty($data['xpcBackReference'])
            && is_array($data['fields'])
            && !empty($data['fields'])
            && !empty($data['fields']['token']);
    }

    /**
     * Save init payment form data to session
     *
     * @param \XLite\Model\Payment\Transaction $transaction Transaction OPTIONAL
     * @param array                            $data        Form data OPTIONAL
     *
     * @return void 
     */
    public function saveInitDataToSession(\XLite\Model\Payment\Transaction $transaction = null, $data = null)
    {
        if ($transaction && $this->isInitDataValid($data)) {
            $formData = \XLite\Core\Session::getInstance()->xpc_form_data;

            if (!is_array($formData)) {
                $formData = array();
            }

            $formData[$transaction->getPaymentMethod()->getMethodId()] = $data;

        } else {
            $formData = null;
        }

        \XLite\Core\Session::getInstance()->xpc_form_data = $formData;
    }

    /**
     * Get redirect form fields list
     *
     * @param \XLite\Model\Payment\Transaction $transaction Transaction
     *
     * @return array
     */
    public function getFormFields(\XLite\Model\Payment\Transaction $transaction)
    {
        // 1. Try to get data from session
        $data = $this->getInitDataFromSession($transaction);

        if (!$data) {

            // 2. Try to get data from X-Payments
            $data = $this->getInitDataFromXpayments($transaction);
        }

        if ($data) {

            // Save X-Payments transaction id in transaction data
            $transaction->setDataCell('xpc_txnid', $data['txnId'], 'X-Payments transaction id', 'C');

            // Save back refernece to transaction from  X-Payments 
            $transaction->setDataCell('xpcBackReference', $data['xpcBackReference'], 'X-Payments back reference', 'C');

            try {
                \XLite\Core\Database::getEM()->flush();

            } catch (Exception $e) {
                $this->setXpcInitError($transaction, 'Internal error. Unable to update transaction');
            }

            $data = $data['fields'];

            if (version_compare(\XLite\Core\Config::getInstance()->CDev->XPaymentsConnector->xpc_api_version, '1.6') >= 0) {
                $data['allow_save_card'] = $this->getAllowSaveCard($transaction);
            }

        } else {

            $data = array();
        }

        return $data;
    }

    /**
     * Create a cart with fake item
     *
     * @param \XLite\Model\Profile $profile Customer's profile for whom the cart is created for
     * @param \XLite\Model\Payment\Method $paymentMethod Payment methood
     * @param $total Cart total
     * @param $itemName Name of the fake item
     * @param $itemSku SKU of the fake item
     * @param $addressId Address ID 
     *
     * @return \XLite\Model\Cart
     */
    public function createFakeCart(\XLite\Model\Profile $profile, \XLite\Model\Payment\Method $paymentMethod, $total, $itemName, $itemSku, $addressId = false)
    {
        $cart = new \XLite\Model\Cart;

        $cart->setTotal($total);
        $cart->setDate(time());
        $cart->setOrderNumber(\XLite\Core\Database::getRepo('XLite\Model\Order')->findNextOrderNumber());

        $cart->setProfile($profile);

        $cart->setCurrency(\XLite::getInstance()->getCurrency());
        $cart->setPaymentMethod($paymentMethod, $total);

        \XLite\Core\Database::getEM()->persist($cart);
        \XLite\Core\Database::getEM()->flush();

        $item = new \XLite\Model\OrderItem;
        $item->setName($itemName);
        $item->setSku($itemSku);
        $item->setPrice($total);
        $item->setAmount(1);
        $item->setTotal($total);
        $item->setXpcFakeItem(true);

        \XLite\Core\Database::getEM()->persist($item);
        \XLite\Core\Database::getEM()->flush();

        $cart->addItem($item);

        if ($addressId) {
            $address = \XLite\Core\Database::getRepo('\XLite\Model\Address')->find($addressId);

            if ($address) {
                $address->setIsBilling(true);
                $address->setIsShipping(true);

            }
        }

        if (count($cart->getPaymentTransactions()) == 0) {

            // We cannot use first open transaction later, so we need to create it
            $transaction = new \XLite\Model\Payment\Transaction;

            $transaction->setPaymentMethod($paymentMethod);

            $transaction->setValue($total);

            $cart->addPaymentTransactions($transaction);
            $transaction->setOrder($cart);
        }

        return $cart;
    }

    /**
     * Prepare address data
     *
     * @param \XLite\Model\Profile $profile Customer's profile
     * @param $type Address type, billing or shipping
     *
     * @return array
     */
    protected function prepareAddress(\XLite\Model\Profile $profile, $type = 'billing')
    {
        $result = array();

        $addressFields = array(
            'firstname' => 'N/A',
            'lastname'  => '',
            'address'   => 'N/A',
            'city'      => 'N/A',
            'state'     => 'N/A',
            'country'   => 'XX', // WA fix for MySQL 5.7 with strict mode
            'zipcode'   => 'N/A',
            'phone'     => '',
            'fax'       => '',
            'company'   => '',
        );

        $repo = \XLite\Core\Database::getRepo('\XLite\Model\AddressField');

        $type = $type . 'Address';

        foreach ($addressFields as $field => $defValue) {

            $method = 'address' == $field ? 'street' : $field;
            $address = $profile->$type;

            if (
                $address
                && ($repo->findOneBy(array('serviceName' => $method)) || method_exists($address, 'get' . $method))
                && $address->$method
            ) {
                $result[$field] = is_object($profile->$type->$method)
                    ? $profile->$type->$method->getCode()
                    : $profile->$type->$method;
            }

            if (empty($result[$field])) {
                $result[$field] = $defValue;
            }
        }

        $result['email'] = $profile->getLogin();

        return $result;
    }

    /**
     * Round currency
     *
     * @param float $data Data
     *
     * @return float
     */
    protected function roundCurrency($data)
    {
        return sprintf('%01.2f', round($data, 2));
    }

    /**
     * Get customer unique ID for Kount UNIQ field
     *
     * @param \XLite\Model\Order            $cart           X-Cart shopping cart
     *
     * @return string
     */
    protected function getKountCustomerUniq(\XLite\Model\Order $cart)
    {
        $profile = $cart->getProfile();

        $uniq = $profile->getLogin();

        return substr($uniq, 0, 32);
    }

    /**
     * Prepare shopping cart data
     *
     * @param \XLite\Model\Order            $cart           X-Cart shopping cart
     * @param \XLite\Model\Payment\Method   $paymentMethod  Payment method
     * @param integer                       $refId          Transaction ID OPTIONAL
     * @param boolean                       $forceAuth      Force enable AUTH mode OPTIONAL
     *
     * @return array
     */
    public function prepareCart(\XLite\Model\Order $cart, \XLite\Model\Payment\Method $paymentMethod, $refId = null, $forceAuth = false)
    {
        $config = \XLite\Core\Config::getInstance()->CDev->XPaymentsConnector;

        $profile = $cart->getProfile();

        // To make shipping correct
        $cart->setXpcForceCalcFlag();
        $cart->calculate();
        $cart->setXpcForceCalcFlag(false);

        if ($cart->getOrderNumber()) {

            $description = 'Order #' . $cart->getOrderNumber();

        } elseif (
            $cart->getFirstOpenPaymentTransaction()
            && $cart->getFirstOpenPaymentTransaction()->getPublicId()
        ) {
            
            $description = 'Payment transaction: ' . $cart->getFirstOpenPaymentTransaction()->getPublicId();

        } else {

            $description = '';
        }

        $result = array(
            'login'                => $profile->getLogin() . ' (User ID #' . $profile->getProfileId() . ')',
            'items'                => array(),
            'currency'             => \XLite::getInstance()->getCurrency()->getCode(),
            'shippingCost'         => 0.00,
            'taxCost'              => 0.00,
            'discount'             => 0.00,
            'totalCost'            => 0.00,
            'description'          => $description,
            'merchantEmail'        => \XLite\Core\Config::getInstance()->Company->orders_department,
            'forceTransactionType' => $forceAuth ? 'A' : '',
        );

        // Send customer unique Id for Kount UNIQ field (API v1.6 and higher)
        if (version_compare($config->xpc_api_version, '1.6') >= 0) {
            $result['kountCustomerUniq'] = $this->getKountCustomerUniq($cart);
        }

        if (
            $profile->getBillingAddress()
            && $profile->getShippingAddress()
        ) {

            $result['billingAddress'] = $this->prepareAddress($profile);
            $result['shippingAddress'] = $this->prepareAddress($profile, 'shipping');

        } elseif (
            $profile->getBillingAddress()
            && !$profile->getShippingAddress()
        ) {

            $result['billingAddress'] = $result['shippingAddress'] = $this->prepareAddress($profile);

        } else { 

            $result['billingAddress'] = $result['shippingAddress'] = $this->prepareAddress($profile, 'shipping');
        }  

        // Set items
        if ($cart->getItems()) {

            foreach ($cart->getItems() as $item) {

                $itemElement = array(
                    'sku'      => strval($item->getSku() ? $item->getSku() : $item->getName()),
                    'name'     => strval($item->getName() ? $item->getName() : $item->getSku()),
                    'price'    => $this->roundCurrency($item->getPrice()),
                    'quantity' => $item->getAmount(),
                );

                if (!$itemElement['sku']) {
                    $itemElement['sku'] = 'N/A';
                }

                if (!$itemElement['name']) {
                    $itemElement['name'] = 'N/A';
                }

                $result['items'][] = $itemElement;
            }
        }

        // Set costs
        $result['shippingCost'] = $this->roundCurrency(
            $cart->getSurchargesSubtotal(\XLite\Model\Base\Surcharge::TYPE_SHIPPING, false)
        );
        $result['taxCost'] = $this->roundCurrency(
            $cart->getSurchargesSubtotal(\XLite\Model\Base\Surcharge::TYPE_TAX, false)
        );
        $result['totalCost'] = $this->roundCurrency($cart->getTotal());
        $result['discount'] = $this->roundCurrency(
            abs($cart->getSurchargesSubtotal(\XLite\Model\Base\Surcharge::TYPE_DISCOUNT, false))
        );

        return $result;
    }

    /**
     * Compose error message from message and code
     * (It's here for the regexp in the next method)
     *
     * @param string $code Error code
     * @param string $message Error message
     *
     * @return string 
     */
    public function composeErrorMessage($code = '', $message = '')
    {
        $error = '';

        if ($code) {

            $error = 'X-Payments error (code: ' . $code . ') ';

            if ($message) {
                $error .= ' ' . $message;
            }

        } elseif ($message) {

            $error = $message;

        } else {

            $error = \XLite\Model\Payment\Transaction::getDefaultFailedReason(); 
        }

        return $error;
    }

    /**
     * Parse message to extract error code
     *
     * @param string $message Message
     *
     * @return array
     */
    public function parseErrorMessage($message)
    {
        $code = false;

        if (preg_match('/X-Payments error \(code: (\d+)\) (.*)$/', $message, $m)) {

            $code = $m[1];
            $message = $m[2];
        }

        return array($code, $message);
    }    

    /**
     * The payment method should be changed according to the error message or not
     *
     * @param string $message Error message
     *
     * @return bool 
     */
    protected function isChangeMethodMessage($message)
    {
        static $codes = array(
            '502', // Payment configuration is not initialized
            '503', // Unable to create a new payment
            '504', // Specified currency is not allowed
            '505', // Payment interface template files have been modified
            '506', // API Version mismatch
        );

        list($code, $message) = $this->parseErrorMessage($message);

        return in_array($code, $codes);
    }

    /**
     * Set X-Payments API error to:
     *  - Logs
     *  - Transaction data
     *  - Controller
     *
     * @param \XLite\Model\Payment\Transaction $transaction Transaction
     * @param string $message Error message
     *
     * @return void
     */
    protected function setXpcInitError(\XLite\Model\Payment\Transaction $transaction, $message = '')
    {
        self::writeLogError('X-Paymets payment initialization failed: ' . $message);

        $transaction->setDataCell('status', $message, 'X-Payments error', 'C');
        $transaction->setNote($message);

        $iframe = \XLite::getController()->getIframe();

        $iframe->setError($message);

        if (\XLite::getController()->isCheckoutReady()) {

            $type = $this->isChangeMethodMessage($message)
                ? \XLite\Module\CDev\XPaymentsConnector\Core\Iframe::IFRAME_CHANGE_METHOD
                : \XLite\Module\CDev\XPaymentsConnector\Core\Iframe::IFRAME_ALERT;

            $iframe->setType($type);

        } else {
            $iframe->setType(\XLite\Module\CDev\XPaymentsConnector\Core\Iframe::IFRAME_DO_NOTHING);
        }

        $this->clearInitDataFromSession();

        $iframe->finalize();
    }

    /**
     * Check payment status at X-Payments to make sure token is valid
     *
     * @param \XLite\Model\Payment\Transaction $transaction Transaction
     *
     * @return bool
     */
    protected function checkInitPaymentStatus(\XLite\Model\Payment\Transaction $transaction)
    {
        $result = false;

        $txnId = $transaction->getDataCell('xpc_txnid')
            ? $transaction->getDataCell('xpc_txnid')->getValue()
            : false; 

        if ($txnId) {

            $info = $this->requestPaymentInfo($txnId);

            if ($info->isSuccess()) {

                $response = $info->getResponse();

                // For API 1.5 and before payment status must be New/Pending
                // API 1.6 and later allows to return to the same payment and pay again if it was declined
                $result = (
                    version_compare(\XLite\Core\Config::getInstance()->CDev->XPaymentsConnector->xpc_api_version, '1.6') >= 0
                    || \XLite\Module\CDev\XPaymentsConnector\Model\Payment\Processor\XPayments::STATUS_NEW == $response['status']
                );
            }
        }

        return $result;
    }

    /**
     * Get init payment form data from session 
     *
     * @param \XLite\Model\Payment\Transaction $transaction Transaction
     *
     * @return array || bool
     */
    protected function getInitDataFromSession(\XLite\Model\Payment\Transaction $transaction)
    {
        $paymentId = $transaction->getPaymentMethod()->getMethodId();

        $formData = \XLite\Core\Session::getInstance()->xpc_form_data;

        if (
            $formData 
            && isset($formData[$paymentId]) 
            && $this->isInitDataValid($formData[$paymentId])
            && $this->checkInitPaymentStatus($transaction)
        ) {

            $data = $formData[$paymentId];

        } else {

            $data = false;
        }

        return $data;
    }

    /**
     * Get init payment form data from XPayments
     *
     * @param \XLite\Model\Payment\Transaction $transaction Transaction
     *
     * @return array 
     */
    protected function getInitDataFromXpayments(\XLite\Model\Payment\Transaction $transaction)
    {
        $init = $this->requestPaymentInit(
            $transaction,
            \XLite\Model\Cart::getInstance()
        );

        if ($init->isSuccess()) {

            $response = $init->getResponse();

            $data = array(
                'xpcBackReference' => $response['xpcBackReference'],
                'txnId'            => $response['txnId'],
                'fields'           => $response['fields'],
            );

            $this->saveInitDataToSession($transaction, $data);

        } else {

            $data = null;
            $this->setXpcInitError($transaction, $init->getError());
        }

        return $data;
    }

    /**
     * Get return to the store URL 
     *
     * @param string $xpcBackReference Reference between X-Payments payment and X-Cart transaction 
     * @param bool $forZeroAuth Return to add to card page or to checkout
     *
     * @return string
     */
    public static function getReturnUrl($xpcBackReference, $forZeroAuth = false)
    {
        if (!$forZeroAuth) {

            // Return to the regular checkout page

            $url = \XLite\Core\Converter::buildUrl(
                'payment_return',
                'return',
                array('xpcBackReference' => $xpcBackReference),
                \XLite::getCustomerScript()
            );

        } else {

            // return to the add new card page

            $url = \XLite\Core\Converter::buildUrl(
                'add_new_card',
                'return',
                array('xpcBackReference' => $xpcBackReference),
                \XLite::getCustomerScript()
            );
        }

        // Last parameter is necessary to remove XID from URL
        return \XLite\Core\URLManager::getShopURL($url, true, array(), null, false);
    }

    /**
     * Get callback URL
     *
     * @param string $xpcBackReference Reference between X-Payments payment and X-Cart transaction
     *
     * @return string
     */
    public static function getCallbackUrl($xpcBackReference)
    {
        $url = \XLite\Core\Converter::buildUrl(
            'callback',
            'callback',
            array('xpcBackReference' => $xpcBackReference),
            \XLite::getCustomerScript()
        );

        // Last parameter is necessary to remove XID from URL
        return \XLite\Core\URLManager::getShopURL($url, true, array(), null, false);
    }

    /**
     * For compatibiilty with mobile devices iframe is not used 
     *
     * @return bool
     */
    public static function isMobileDeviceCompatible()
    {
        $mobileExists = method_exists('\XLite\Core\Request', 'isMobileDevice')
            && \XLite\Core\Request::isMobileDevice();

        $mobileEnabled = method_exists(\XLite\Core\Request::getInstance(), 'isMobileEnabled')
            && \XLite\Core\Request::isMobileEnabled();

        return $mobileExists && $mobileEnabled;
    }

    /**
     * Get Data for specific API versions 
     * 
     * @return array
     */
    protected function getExtraData()
    {
        $config = \XLite\Core\Config::getInstance()->CDev->XPaymentsConnector;

        $data = array();

        // API v1.3 and higher
        if (version_compare($config->xpc_api_version, '1.3') >= 0) {

            // For mobile devices the tempate is default, and should be displayed on a separate page
            if (self::isMobileDeviceCompatible()) {
                $data['template'] = 'default';
            }

            if (version_compare($config->xpc_api_version, '1.6') < 0) {
                // Save card at checkout flag
                $data['saveCard'] = 'Y' == \XLite\Core\Request::getInstance()->save_card
                    ? 'Y'
                    : 'N';
            }
        }
        
        return $data;
    }

    /**
     * Get data hash for initial payment request
     *
     * @param int $confId Configuration ID in X-Payments
     * @param string $xpcBackReference Reference between X-Payments payment and X-Cart transaction
     * @param array $preparedCart Cart data
     *
     * @return array
     */
    protected function getInitRequestData($confId, $xpcBackReference, $preparedCart)
    {
        $data = array(
            'confId'      => $confId,
            'refId'       => $xpcBackReference,
            'cart'        => $preparedCart,
            'language'    => \XLite\Core\Session::getInstance()->getLanguage()->getCode(),
            'returnUrl'   => self::getReturnUrl($xpcBackReference),
            'callbackUrl' => self::getCallbackUrl($xpcBackReference),
        );

        // Add API-specific data
        $data += $this->getExtraData();

        return $data;
    }

    /**
     * Checks if Save Card checkbox must be forced to be Required
     *
     * @param \XLite\Model\Payment\Transaction $transaction Payment transaction
     *
     * @return string
     */
    protected function getAllowSaveCard(\XLite\Model\Payment\Transaction $transaction)
    {
        // Check if save card feature is available for customer
        $showToUser = (
                !\XLite\Core\Auth::getInstance()->isAnonymous() && \XLite\Core\Auth::getInstance()->isLogged()
                || \XLite\Core\Session::getInstance()->order_create_profile
            );
        $showForPayment = 'Y' == $transaction->getPaymentMethod()->getSetting('saveCards');

        if ($showToUser && $showForPayment) {
            if ($transaction->getProfile()->getPendingZeroAuth() == $transaction->getPublicId()) {
                // This is a "Card setup" transaction 
                $allowSaveCard = static::SAVE_CARD_REQUIRED;
            } else {
                $allowSaveCard = static::SAVE_CARD_OPTIONAL;
            }
        } else {
            $allowSaveCard = static::SAVE_CARD_DISABLED;
        }

        return $allowSaveCard;
    }

    /**
     * Send request to X-Payments to initialize new payment
     *
     * @param \XLite\Model\Payment\Transaction $transaction Payment transaction
     * @param \XLite\Model\Cart           $cart          Shopping cart info
     * @param boolean                     $forceAuth     Force enable AUTH mode OPTIONAL
     *
     * @return array
     */
    protected function requestPaymentInit(\XLite\Model\Payment\Transaction $transaction, \XLite\Model\Cart $cart, $forceAuth = false)
    {
        $paymentMethod = $transaction->getPaymentMethod();

        // Prepare cart
        $preparedCart = $this->prepareCart($cart, $paymentMethod, null, $forceAuth);

        if ($cart && $preparedCart) {

            $xpcBackReference = $transaction->getPublicId();

            // Send request to X-Payments
            $result = $this->apiRequest->send(
                'payment',
                'init',
                $this->getInitRequestData($paymentMethod->getSetting('id'), $xpcBackReference, $preparedCart)
            );

            if ($result->isSuccess()) {

                $response = $result->getResponse();

                // Set fields for the "Redirect to X-Payments" form
                $result->setResponse(array(
                    'xpcBackReference' => $xpcBackReference,
                    'txnId'            => $response['txnId'],
                    'module_name'      => $paymentMethod->getSetting('moduleName'),
                    'url'              => \XLite\Core\Config::getInstance()->CDev->XPaymentsConnector->xpc_xpayments_url . '/payment.php',
                    'fields'           => array(
                        'target' => 'main',
                        'action' => 'start',
                        'token'  => $response['token'],
                    ),
                ));
            }

        } else {

            // Something is wrong with the cart. Should not ever happen
            $result = new \XLite\Module\CDev\XPaymentsConnector\Transport\Response;
            $result->setError('Unable to prepare cart data');
        }

        return $result;
    }

    /**
     * Get X-Payments Connector configuration errors
     *
     * @return integer
     */
    protected function getModuleSystemErrors()
    {
        $config = \XLite\Core\Config::getInstance()->CDev->XPaymentsConnector;

        $failed = 0;

        // Check shopping cart id
        if (
            empty($config->xpc_shopping_cart_id)
            || !preg_match('/^[\da-f]{32}$/Ss', $config->xpc_shopping_cart_id)
        ) {
            $failed |= static::XPC_SYSERR_CARTID;
        }

        // Check URL
        if (empty($config->xpc_xpayments_url)) {
            $failed |= static::XPC_SYSERR_URL;
        }

        $parsedURL = @parse_url($config->xpc_xpayments_url);

        if (
            !$parsedURL
            || !isset($parsedURL['scheme'])
            || !in_array($parsedURL['scheme'], array( 'https', 'http'))
        ) {
            $failed |= static::XPC_SYSERR_URL;
        }

        // Check public key
        if (empty($config->xpc_public_key)) {
            $failed |= static::XPC_SYSERR_PUBKEY;
        }

        // Check private key
        if (empty($config->xpc_private_key)) {
            $failed |= static::XPC_SYSERR_PRIVKEY;
        }

        // Check private key password
        if (empty($config->xpc_private_key_password)) {
            $failed |= static::XPC_SYSERR_PRIVKEYPASS;
        }

        return $failed;
    }

    /**
     * Check module requirements
     *
     * @return integer
     */
    public function checkRequirements()
    {
        $code = 0;

        if (!function_exists('curl_init')) {
            $code = $code | static::REQ_CURL;
        }

        if (
            !function_exists('openssl_pkey_get_public') || !function_exists('openssl_public_encrypt')
            || !function_exists('openssl_get_privatekey') || !function_exists('openssl_private_decrypt')
            || !function_exists('openssl_free_key')
        ) {
            $code = $code | static::REQ_OPENSSL;
        }

        if (!class_exists('DOMDocument')) {
            $code = $code | static::REQ_DOM;
        }

        return $code;
    }

    /**
     * Log error
     *
     * @param string $msg Error message
     *
     * @return void 
     */
    protected static function writeLogError($msg)
    {
        \XLite\Logger::getInstance()->logCustom(self::LOG_FILE_ERROR, $msg);
    }

    /**
     * Get API request
     *
     * @param array $data
     *
     * @return sting
     */
    public function getApiRequest()
    {
        return $this->apiRequest;
    }

    /**
     * Get decrypted and processed update data
     *
     * @return array
     */
    public function getUpdateData()
    {
        return static::$updateData;
    }

    /**
     * Set decrypted and processed update data
     *
     * @param $data array Decrypted and processed update data
     *
     * @return void
     */
    public function setUpdateData($data)
    {
        static::$updateData = $data;
    }

    /**
     * Wrapper for encryption of the response
     *
     * @param string $data
     *
     * @return array
     */
    public function processApiResponse($data)
    {
        $data = $this->apiRequest->processApiResponse($data);

        $this->setUpdateData($data);

        return $data;
    }

    /**
     * Wrapper for decryption of the response
     *
     * @param array $data
     *
     * @return sting
     */
    public function encryptRequest($data)
    {
        $xml = $this->apiRequest->convertHashToXml($data);
        return $this->apiRequest->encryptXml($xml);
    }

    /**
     * Constructor
     *
     * @return void 
     */
    public function __construct()
    {
        parent::__construct();

        $this->apiRequest = new \XLite\Module\CDev\XPaymentsConnector\Core\ApiRequest;
    }
}
