<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\OgoneEcommerce\Model\Payment\Processor;

/**
 * OgoneEcommerce processor
 */
class OgoneEcommerce extends \XLite\Model\Payment\Base\WebBased
{
    /**
     * Logging the data under OgoneEcommerce
     * Available if developer_mode is on in the config file
     *
     * @param mixed $data Data
     *
     * @return void
     */
    protected static function log($data)
    {
        if (LC_DEVELOPER_MODE) {
            \XLite\Logger::logCustom('OgoneEcommerce', $data);
        }
    }

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
     * Check - payment method is configured or not
     *
     * @param \XLite\Model\Payment\Method $method Payment method
     *
     * @return boolean
     */
    public function isConfigured(\XLite\Model\Payment\Method $method)
    {
        return parent::isConfigured($method)
            && $method->getSetting('pspid')
            && $method->getSetting('shaIn')
            && $method->getSetting('shaOut')
            && $method->getSetting('mode');
    }

    /**
     * Get return type
     *
     * @return string
     */
    public function getReturnType()
    {
        return self::RETURN_TYPE_HTML_REDIRECT;
    }

    /**
     * Returns the list of settings available for this payment processor
     *
     * @return array
     */
    public function getAvailableSettings()
    {
        return array(
            'pspid',
            'shaIn',
            'shaOut',
            'prefix',
            'mode'
        );
    }

    /**
     * Get return request owner transaction or null
     *
     * @return \XLite\Model\Payment\Transaction
     */
    public function getReturnOwnerTransaction()
    {
        $requestData = \XLite\Core\Request::getInstance()->getData();

        return isset($requestData[static::RETURN_TXN_ID]) && $requestData[static::RETURN_TXN_ID]
            ? \XLite\Core\Database::getRepo('XLite\Model\Payment\Transaction')->findOneByPublicTxnId($requestData[static::RETURN_TXN_ID])
            : null;
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

        $request = \XLite\Core\Request::getInstance();

        static::log(
            array('request' => $request->getData())
        );

        $status = '';
        $notes = array();
        if ($request->type == 'accept') {
            $status = $transaction::STATUS_SUCCESS;
            $this->setDetail('result', 'Accept', 'Result');
            $notes[] = 'Accept';

        } elseif ($request->type == 'cancel') {
            $status = $transaction::STATUS_CANCELED;
            $this->setDetail('result', 'Cancel', 'Result');
            $notes[] = 'Cancel';

        } elseif ($request->type == 'decline') {
            $status = $transaction::STATUS_FAILED;
            $this->setDetail('result', 'Decline', 'Result');
            $notes[] = 'Decline';

        } elseif ($request->type == 'exception') {
            $status = $transaction::STATUS_FAILED;
            $this->setDetail('result', 'Exception', 'Result');
            $notes[] = 'Exception';

        } else {
            $status = $transaction::STATUS_FAILED;
            $this->setDetail('result', 'Result is incorrect', 'Result');
            $notes[] = 'Result is incorrect';
        }

        if (!$request->orderID) {
            $status = $transaction::STATUS_FAILED;
            $this->setDetail('order_checking', 'Order ID is empty', 'Checking');
            $notes[] = 'Order ID is empty';
        }

        $fields = $request->getGetData();
        unset($fields['SHASIGN'], $fields['target'], $fields['type'], $fields[self::RETURN_TXN_ID]);

        $hash = $this->generateSign($fields, $this->getSetting('shaOut'));

        if ($hash != $request->SHASIGN) {
            $status = $transaction::STATUS_FAILED;
            $this->setDetail('sha_verification', 'SHA verification failed', 'Verification');
            $notes[] = 'SHA verification failed';
        }

        if (!$this->checkTotal($request->amount)) {
            $status = $transaction::STATUS_FAILED;
            $this->setDetail('total_checking', 'Total checking failed', 'Checking');
            $notes[] = 'Total checking failed';
        }

        if (!$this->checkCurrency($request->CURRENCY)) {
            $status = $transaction::STATUS_FAILED;
            $this->setDetail('currency_checking', 'Currency checking failed', 'Checking');
            $notes[] = 'Currency checking failed';
        }

        $this->transaction->setStatus($status);
        $this->transaction->setNote(implode('. ', $notes));
    }

    /**
     * Get settings widget or template
     *
     * @return string Widget class name or template path
     */
    public function getSettingsWidget()
    {
        return 'modules/XC/OgoneEcommerce/config.twig';
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
     * Get redirect form URL
     *
     * @return string
     */
    protected function getFormURL()
    {
        return \XLite\View\FormField\Select\TestLiveMode::TEST === $this->getSetting('mode')
            ? 'https://secure.ogone.com/ncol/test/orderstandard.asp'
            : 'https://secure.ogone.com/ncol/prod/orderstandard.asp';
    }

    /**
     * Get redirect form fields list
     *
     * @return array
     */
    protected function getFormFields()
    {
        $fields = array(
            'PSPID'         => $this->getSetting('pspid'),
            'ORDERID'       => $this->getTransactionId(),
            'AMOUNT'        => $this->getFormattedAmount($this->getOrder()->getTotal()),
            'CURRENCY'      => $this->transaction->getCurrency()->getCode(),
            'LANGUAGE'      => $this->getFormattedLanguage($this->getProfile()),
            'CN'            => $this->getFormattedName($this->getProfile()->getBillingAddress()),
            'EMAIL'         => $this->getProfile()->getLogin(),
            'OWNERADDRESS'  => $this->getProfile()->getBillingAddress()->getStreet(),
            'OWNERZIP'      => $this->getProfile()->getBillingAddress()->getZipcode(),
            'OWNERTOWN'     => $this->getProfile()->getBillingAddress()->getCity(),
            'OWNERCTY'      => $this->getProfile()->getBillingAddress()->getCountry()->getCode3(),
            'OWNERTELNO'    => $this->getProfile()->getBillingAddress()->getPhone(),
            'ACCEPTURL'     => $this->getPaymentReturnURL('accept'),
            'DECLINEURL'    => $this->getPaymentReturnURL('decline'),
            'EXCEPTIONURL'  => $this->getPaymentReturnURL('exception'),
            'CANCELURL'     => $this->getPaymentReturnURL('cancel'),
        );
        $hash = $this->generateSign($fields, $this->getSetting('shaIn'));
        $fields['SHASIGN'] = $hash;

        static::log(
            array('fields' => $fields)
        );

        return $fields;
    }

    /**
     * Generate SHA Sign from fields
     *
     * @param array  $fields     Array of fields
     * @param string $passphrase Passphrase
     *
     * @return string
     */
    protected function generateSign($fields, $passphrase)
    {
        $formattedFields = array();
        foreach ($fields as $k => $v) {
            $value = trim($v);
            if (isset($value) &&  '' != $value) {
                $formattedFields[strtoupper($k)] = $v;
            }
        }
        ksort($formattedFields);
        $separator = '=';
        $hash = '';
        foreach ($formattedFields as $k => $v) {
             $hash = $hash
                 . $k
                 . $separator
                 . $v
                 . $passphrase;
        }
        $hash = strtoupper(sha1($hash));

        return $hash;
    }

    /**
     * Format name for request. (firstname + lastname from shipping/billing address)
     *
     * @param \XLite\Model\Address $address Address model (could be shipping or billing address)
     *
     * @return string
     */
    protected function getFormattedName($address)
    {
        return $address->getFirstname()
        . ' ' . $address->getLastname();
    }

    /**
     * Format amount for request (amount * 100) without delimiters
     *
     * @param float $amount Amount
     *
     * @return string
     */
    protected function getFormattedAmount($amount)
    {
        return strval($this->transaction->getCurrency()->roundValueAsInteger($amount));
    }

    /**
     * Format language for request ("language_country"="en_EN")
     *
     * @param \XLite\Model\Profile $profile Profile model
     *
     * @return string
     */
    protected function getFormattedLanguage($profile)
    {
        return
            \XLite\Core\Session::getInstance()->getLanguage()->getCode()
            . '_'
            . strtoupper($profile->getBillingAddress()->getCountry()->getCode());
    }

    /**
     * Get payment return URL, type: accept, decline, exception, cancel
     *
     * @param string $type Type of return
     *
     * @return string
     */
    protected function getPaymentReturnURL($type)
    {
        return \XLite::getInstance()->getShopURL(
            \XLite\Core\Converter::buildURL(
                'payment_return', null,
                array(
                    self::RETURN_TXN_ID => $this->transaction->getPublicTxnId(),
                    'type'              => $type,
                )
            ),
            \XLite\Core\Config::getInstance()->Security->customer_security
        );
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
        return array(
            'AED', 'ANG', 'ARS', 'AUD', 'AWG',
            'BGN', 'BRL', 'BYR', 'CAD', 'CHF',
            'CNY', 'CZK', 'DKK', 'EEK', 'EGP',
            'EUR', 'GBP', 'GEL', 'HKD', 'HRK',
            'HUF', 'ILS', 'ISK', 'JPY', 'KRW',
            'LTL', 'LVL', 'MAD', 'MXN', 'NOK',
            'NZD', 'PLN', 'RON', 'RUB', 'SEK',
            'SGD', 'SKK', 'THB', 'TRY', 'UAH',
            'USD', 'XAF', 'XOF', 'XPF', 'ZAR'
        );
    }

}
