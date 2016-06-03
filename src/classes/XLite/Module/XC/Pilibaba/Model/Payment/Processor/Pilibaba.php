<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Pilibaba\Model\Payment\Processor;

/**
 * Pilibaba payment processor
 */
class Pilibaba extends \XLite\Model\Payment\Base\CreditCard
{
    /**
     * Constructor
     */
    protected function __construct()
    {
        parent::__construct();

        \XLite\Module\XC\Pilibaba\Main::includeLibrary();
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
            && $method->getSetting('merchantNO')
            && $method->getSetting('secretKey');
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

        if (LC_DEVELOPER_MODE) {
            static::log('Callback entered with following data: ' . var_export($_REQUEST, true));
        }

        $payResult = \PilipayPayResult::fromRequest();

        if ($payResult->verify($this->getSetting('secretKey'))) {

            if ($payResult->isSuccess()){
                $this->transaction->setStatus(
                    \XLite\Model\Payment\Transaction::STATUS_SUCCESS
                );

            } else {
                static::log('Pilibaba callback was not successful');
                $this->transaction->setNote('Transaction failed');
                $this->transaction->setStatus(
                    \XLite\Model\Payment\Transaction::STATUS_FAILED
                );
            }

            if ($payResult->getCustomerMail()
                && $this->transaction->getOrder()
                && $this->transaction->getOrder()->getProfile()
            ) {
                $this->transaction->getOrder()->getProfile()->setLogin(
                    $payResult->getCustomerMail()
                );
                $this->transaction->getOrder()->getProfile()->update();
            }

            $payResult->returnDealResultToPilibaba(
                $payResult->isSuccess(),
                false
            );

            $transaction->registerTransactionInOrderHistory('callback');

        } else {
            static::log('Callback was not verified');
            $this->markCallbackRequestAsInvalid(
                static::t('Callback was not verified')
            );
        }
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
     * Get initial transaction type (used when customer places order)
     *
     * @param \XLite\Model\Payment\Method $method Payment method object OPTIONAL
     *
     * @return string
     */
    public function getInitialTransactionType($method = null)
    {
        return self::OPERATION_AUTH === ($method ? $method->getSetting('type') : $this->getSetting('type'))
            ? \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_AUTH
            : \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_SALE;
    }

    /**
     * Returns the list of settings available for this payment processor
     *
     * @return array
     */
    public function getAvailableSettings()
    {
        return array(
            'merchantNO',
            'secretKey',
            'currency',
            'warehouse',
        );
    }

    /**
     * Get payment method of this processor
     *
     * @return \XLite\Model\Payment\Method
     */
    protected function getPaymentMethod()
    {
        $method = null;

        if ($this->transaction) {
            $method = $this->transaction->getPaymentMethod();
        } else {
            $method = \XLite\Module\XC\Pilibaba\Main::getPaymentMethod();
        }

        return $method;
    }

    /**
     * Get setting value by name
     *
     * @param string $name Name
     *
     * @return mixed
     */
    protected function getSetting($name)
    {
        return parent::getSetting($name) ?: $this->getPaymentMethod()->getSetting($name);
    }

    /**
     * Initialize pilibay library
     *
     * @return  void
     */
    public function initPilipay()
    {
        \PilipayConfig::setUseHttps(false);
        \PilipayConfig::setUseProductionEnv(
            !$this->isTestMode(
                $this->getPaymentMethod()
            )
        );
        \PilipayLogger::instance()->setHandler(
            function($level, $msg) {
                static::log(
                    sprintf('%s %s: %s' . PHP_EOL, date('Y-m-d H:i:s'), $level, $msg)
                );
            }
        );
    }

    /**
     * Update tracking number request
     *
     * @param string $transactionId Transaction id
     *
     * @return string
     */
    public function getBarcodeUrl($transactionId)
    {
        $this->initPilipay();

        $order = new \PilipayOrder();

        $order->orderNo     = $this->getSetting('orderPrefix')
            ? $this->getSetting('orderPrefix') . '_' . $transactionId
            : $transactionId;
        $order->merchantNo  = $this->getSetting('merchantNO');

        $url = '';

        try{
            $url = $order->getBarcodePicUrl();

        } catch (\PilipayError $e) {
            static::log('Exception: ' . $e->getMessage());
        }

        return $url;
    }

    /**
     * Update tracking number request
     *
     * @param string $transactionId     Transaction id
     * @param string $value             Tracking number value
     *
     * @return void
     */
    public function updateTracking($transactionId, $value)
    {
        $this->initPilipay();

        $order = new \PilipayOrder();

        $order->orderNo     = $this->getSetting('orderPrefix')
            ? $this->getSetting('orderPrefix') . '_' . $transactionId
            : $transactionId;
        $order->merchantNo  = $this->getSetting('merchantNO');
        $order->appSecret   = $this->getSetting('secretKey');

        try{
            $order->updateTrackNo($value);
        } catch (\PilipayError $e) {
            static::log('Exception: ' . $e->getMessage());
        }
    }

    /**
     * Mark previous one as failed
     */
    protected function markPreviousTransactionAsFailed()
    {
        $this->transaction->setNote('Transaction reinitiated');
        $this->transaction->setStatus(
            \XLite\Model\Payment\Transaction::STATUS_FAILED
        );
        $this->transaction->registerTransactionInOrderHistory('initial');
    }

    /**
     * Mark previous one as failed and create a new one
     */
    protected function refreshPaymentTransaction()
    {
        $newTransaction = $this->transaction->cloneEntity();
        $newTransaction->setPublicId(null);
        $newTransaction->setPublicTxnId(null);
        $newTransaction->renewTransactionId();
        \XLite\Core\Database::getEM()->persist($newTransaction);
        \XLite\Core\Database::getEM()->flush($newTransaction);

        $this->markPreviousTransactionAsFailed();

        $this->transaction = $newTransaction;
    }

    /**
     * Do initial payment
     *
     * @return string Status code
     */
    protected function doInitialPayment()
    {
        $this->initPilipay();
        $this->refreshPaymentTransaction();

        $order = $this->getOrder();

        $options = array(
            'urls'          => array(
                'callbackUrl'   => $this->getCallbackURL(null, true),
                'returnUrl'     => $this->getReturnURL(null, true),
            ),
            'fees'          => array(
                'shipper'       => $this->getSetting('shippingFee'),
                'tax'           => $order->getSurchargesSubtotal(
                    \XLite\Model\Base\Surcharge::TYPE_TAX,
                    false
                ),
                'discount'      => $order->getSurchargesSubtotal(
                    \XLite\Model\Base\Surcharge::TYPE_DISCOUNT,
                    false
                ),
            )
        );

        $options['credentials'] = array(
            'merchantNO'    => $this->getSetting('merchantNO'),
            'secretKey'     => $this->getSetting('secretKey'),
        );

        if ($this->getSetting('orderPrefix')) {
            $options['orderPrefix'] = $this->getSetting('orderPrefix');
        }

        $status = static::PROLONGATION;
        try{
            $adapter = new \XLite\Module\XC\Pilibaba\Logic\PilipayOrderAdapter($order, $options);

            $piliOrder = $adapter->getResult();

            $submitResult = $piliOrder->submitPatched();

            if (isset($submitResult['statusCode'])
                && $submitResult['statusCode'] === '10000'
                && isset($submitResult['nextUrl'])
            ) {
                \Includes\Utils\Operator::redirect($submitResult['nextUrl']);
            } else {
                $status = static::FAILED;
                \Includes\Utils\Operator::redirect($submitResult['nextUrl']);
            }

        } catch (\PilipayError $e) {
            static::log('Exception: ' . $e->getMessage());
            $status = static::FAILED;
        }

        return $status;
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
        return array_merge(
            parent::getAllowedCurrencies($method),
            array(
                'ARS', 'AUD', 'BRL', 'CAD', 'CHF', 'CLP',
                'CNY', 'COP', 'CZK', 'DKK', 'EUR', 'GBP',
                'HKD', 'HUF', 'IDR', 'ISK', 'JPY', 'KES',
                'KRW', 'MXN', 'MYR', 'NOK', 'NZD', 'PHP',
                'PLN', 'SEK', 'SGD', 'THB', 'TWD', 'USD',
                'VND', 'ZAR',
            )
        );
    }

    /**
     * Logging the data under WorldPay
     * Available if developer_mode is on in the config file
     *
     * @param mixed $data Log data
     *
     * @return void
     */
    public static function log($data)
    {
        \XLite\Logger::logCustom(
            'Pilibaba',
            $data
        );
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
        return $this->isConfigured($method)
            ? \XLite\Core\Converter::buildURL('pilibaba_settings')
            : \XLite\Core\Converter::buildURL('pilibaba_registration');
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
}
