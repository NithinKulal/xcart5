<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Payment\Base;

/**
 * Processor
 */
abstract class Processor extends \XLite\Base
{
    /**
     * Payment procedure result codes
     */
    const PROLONGATION = 'R';
    const SILENT       = 'W';
    const SEPARATE     = 'E';
    const COMPLETED    = 'S';
    const PENDING      = 'P';
    const FAILED       = 'F';


    /**
     * Transaction (cache)
     *
     * @var \XLite\Model\Payment\Transaction
     */
    protected $transaction;

    /**
     * Request cell with transaction input data
     *
     * @var array
     */
    protected $request;

    /**
     * Module processor cache object
     * false                        - it is not initialized yet
     * null                         - no payment processor
     * \XLite\Model\Module class    - payment processor assigned
     *
     * @var boolean|null|\XLite\Model\Module
     */
    protected $moduleCache = false;

    /**
     * Do initial payment
     *
     * @return string Status code
     */
    abstract protected function doInitialPayment();

    /**
     * Get allowed transactions list
     *
     * @return string Status code
     */
    public function getAllowedTransactions()
    {
        return array();
    }

    /**
     * Return tru if backend transaction is allowed for current payment transaction
     *
     * @param \XLite\Model\Payment\Transaction $transaction     Payment transaction object
     * @param string                           $transactionType Backend transaction type
     *
     * @return boolean
     */
    public function isTransactionAllowed(\XLite\Model\Payment\Transaction $transaction, $transactionType)
    {
        $result = false;

        if (in_array($transactionType, $this->getAllowedTransactions())) {

            $methodName = 'is' . \XLite\Core\Converter::convertToCamelCase($transactionType) . 'TransactionAllowed';

            if (method_exists($transaction, $methodName)) {
                // Call transaction type specific method
                $result = $transaction->$methodName();
            }

            if (method_exists($this, $methodName)) {
                $result = $this->{'is' . \XLite\Core\Converter::convertToCamelCase($transactionType) . 'TransactionAllowed'}($transaction);
            }
        }

        return $result;
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
        return null;
    }

    /**
     * Get transaction value
     *
     * @param \XLite\Model\Payment\Transaction $transaction     Payment transaction
     * @param string                           $transactionType Type of transaction
     *
     * @return float
     */
    public function getTransactionValue($transaction, $transactionType)
    {
        $method = 'get' . \XLite\Core\Converter::convertToCamelCase($transactionType) . 'TransactionValue';

        return method_exists($this, $method) ? $this->$method($transaction) : $transaction->getValue();
    }

    /**
     * doTransaction
     *
     * @param \XLite\Model\Payment\Transaction $transaction     Payment transaction object
     * @param string                           $transactionType Backend transaction type
     *
     * @return void
     */
    public function doTransaction(\XLite\Model\Payment\Transaction $transaction, $transactionType)
    {
        if ($this->isTransactionAllowed($transaction, $transactionType)) {

            $methodName = 'do' . \XLite\Core\Converter::convertToCamelCase($transactionType);

            if (method_exists($this, $methodName)) {
                $this->transaction = $transaction;
                $txn = $transaction->createBackendTransaction($transactionType);
                $this->$methodName($txn);

                \XLite\Core\DatabasE::getEM()->flush();

                if ($transaction->getOrder()->renewPaymentStatus()) {
                    // Reset 'recent' property of order if payment status has been changed
                    $transaction->getOrder()->setRecent(0);
                    \XLite\Core\DatabasE::getEM()->flush();
                }
                
                $txn->registerTransactionInOrderHistory();
            }
        }
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
        $this->transaction = $transaction;
        $this->request = $request;

        $this->saveInputData();

        return $this->doInitialPayment();
    }

    /**
     * Get input template
     *
     * @return string|void
     */
    public function getInputTemplate()
    {
        return null;
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
        return array();
    }

    /**
     * Check - payment method is configurable or not
     *
     * @param \XLite\Model\Payment\Method $method Payment method
     *
     * @return boolean
     */
    public function isConfigurable(\XLite\Model\Payment\Method $method)
    {
        return (bool)$this->getConfigurationURL($method);
    }

    /**
     * Get payment method configuration page URL
     *
     * @param \XLite\Model\Payment\Method $method    Payment method
     * @param boolean                     $justAdded Flag if the method is just added via administration panel. Additional init configuration can be provided
     *
     * @return string
     */
    public function getConfigurationURL(\XLite\Model\Payment\Method $method, $justAdded = false)
    {
        $url = null;

        if ($this->getSettingsWidget()) {
            $url = \XLite\Core\Converter::buildURL(
                'payment_method',
                '',
                $justAdded ? array(
                    'method_id'     => $method->getMethodId(),
                    'just_added'    => 1,
                ) : array(
                    'method_id'     => $method->getMethodId(),
                )
            );

        } elseif ($this->hasModuleSettings() && $this->getModuleSettingsForm()) {
            $url = $this->getModuleSettingsForm();
            $url .= (false === strpos($url, '?') ? '?' : '&')
                . 'return=' . urlencode(\XLite\Core\Converter::buildURL(
                    'payment_settings',
                    '',
                    $justAdded ? array(
                        'just_added' => 1,
                        'method_id'  => $method->getMethodId(),
                    ) : array()
                ));
        }

        return $url;
    }

    /**
     * Get settings widget or template
     *
     * @return string Widget class name or template path
     */
    public function getSettingsWidget()
    {
        return null;
    }

    /**
     * Payment method has settings into Module settings section
     *
     * @return boolean
     */
    public function hasModuleSettings()
    {
        return false;
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
        return true;
    }

    /**
     * Return true if payment method supports currency defined in store
     *
     * @param \XLite\Model\Payment\Method $method Payment method
     *
     * @return boolean
     */
    public function isCurrencyApplicable(\XLite\Model\Payment\Method $method)
    {
        $currencies = $this->getAllowedCurrencies($method);

        return !$currencies || in_array(\XLite::getInstance()->getCurrency()->getCode(), $currencies);
    }

    /**
     * Check - payment processor is applicable for specified order or not
     *
     * @param \XLite\Model\Order          $order  Order
     * @param \XLite\Model\Payment\Method $method Payment method
     *
     * @return boolean
     */
    public function isApplicable(\XLite\Model\Order $order, \XLite\Model\Payment\Method $method)
    {
        $currencies = $this->getAllowedCurrencies($method);

        return !$currencies || in_array($order->getCurrency()->getCode(), $currencies);
    }

    /**
     * Get payment method icon path
     *
     * @param \XLite\Model\Order          $order  Order
     * @param \XLite\Model\Payment\Method $method Payment method
     *
     * @return string
     */
    public function getIconPath(\XLite\Model\Order $order, \XLite\Model\Payment\Method $method)
    {
        return null;
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
        return 'checkout/steps/shipping/parts/paymentMethod.twig';
    }

    /**
     * Get processor module
     *
     * @return \XLite\Model\Module|null
     */
    public function getModule()
    {
        if (false === $this->moduleCache) {
            $this->moduleCache = preg_match('/XLite\\\Module\\\(\w+)\\\(\w+)\\\/Ss', get_called_class(), $match)
                ? \XLite\Core\Database::getRepo('XLite\Model\Module')
                    ->findOneBy(array('author' => $match[1], 'name' => $match[2]))
                : null;
        }

        return $this->moduleCache;
    }

    /**
     * Get module settings form
     *
     * @return string|null
     */
    public function getModuleSettingsForm()
    {
        return $this->getModule() ? $this->getModule()->getSettingsForm() : null;
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
     * Generate transaction ID 
     * 
     * @param \XLite\Model\Payment\Transaction $transaction Transaction
     * @param string                           $prefix      Prefix OPTIONAL
     *  
     * @return string
     */
    public function generateTransactionId(\XLite\Model\Payment\Transaction $transaction, $prefix = null)
    {
        $prefix = $prefix ?: $transaction->getPaymentMethod()->getSetting('prefix');

        return $prefix . $transaction->getPublicTxnId();
    }

    /**
     * Get input fields list
     *
     * @return array
     */
    public function getInputDataFields()
    {
        $result = array();

        $labels = $this->getInputDataLabels();
        $accessLevels = $this->getInputDataAccessLevels();

        foreach ($labels as $key => $value) {
            $result[$key] = array(
                'label'       => $value,
                'accessLevel' => isset($accessLevels[$key])
                    ? $accessLevels[$key]
                    : \XLite\Model\Payment\TransactionData::ACCESS_ADMIN,
            );
        }

        return $result;
    }

    /**
     * Get current transaction order
     *
     * @return \XLite\Model\Order
     */
    protected function getOrder()
    {
        return $this->transaction->getOrder();
    }

    /**
     * Get current transaction order profile
     *
     * @return \XLite\Model\Profile
     */
    protected function getProfile()
    {
        return $this->transaction->getOrder()->getProfile();
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
        return $this->transaction
            ? $this->transaction->getPaymentMethod()->getSetting($name)
            : null;
    }

    /**
     * Get transaction ID
     *
     * @param string                           $prefix      Prefix OPTIONAL
     * @param \XLite\Model\Payment\Transaction $transaction Payment transaction OPTIONAL
     * 
     * @return string
     */
    protected function getTransactionId($prefix = null, \XLite\Model\Payment\Transaction $transaction = null)
    {
        if (!$transaction) {
            $transaction = $this->transaction;
        }

        if ($prefix) {
            $transaction->setPublicId($this->generateTransactionId($transaction, $prefix));
        }

        if (!$transaction->getPublicId()) {
            $transaction->setPublicId($this->generateTransactionId($transaction));
        }

        return $transaction->getPublicId();
    }

    /**
     * Save input data
     *
     * @return void
     */
    protected function saveInputData($backendTransaction = null)
    {
        $labels = $this->getInputDataLabels();
        $accessLevels = $this->getInputDataAccessLevels();

        foreach ($this->request as $name => $value) {
            if (isset($accessLevels[$name])) {
                $this->setDetail(
                    $name,
                    $value,
                    isset($labels[$name]) ? $labels[$name] : null,
                    isset($backendTransaction) ? $backendTransaction : null,
                    $accessLevels[$name]
                );
            }
        }
    }

    /**
     * Set transaction detail record
     *
     * @param string                                  $name               Code
     * @param string                                  $value              Value
     * @param string                                  $label              Label OPTIONAL
     * @param \XLite\Model\Payment\BackendTransaction $backendTransaction Backend transaction object OPTIONAL
     *
     * @return void
     */
    protected function setDetail($name, $value, $label = null, $backendTransaction = null, $accessLevel = null)
    {
        $transaction = isset($backendTransaction) ? $backendTransaction : $this->transaction;

        $transaction->setDataCell($name, $value, $label, $accessLevel);
    }

    /**
     * Get input data labels list
     *
     * @return array
     */
    protected function getInputDataLabels()
    {
        return array();
    }

    /**
     * Get input data access levels list
     *
     * @return array
     */
    protected function getInputDataAccessLevels()
    {
        return array();
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
        return array();
    }

    // {{{ Method helpers

    /**
     * Get payment method admin zone icon URL
     *
     * @param \XLite\Model\Payment\Method $method Payment method
     *
     * @return string
     */
    public function getAdminIconURL(\XLite\Model\Payment\Method $method)
    {
        return null;
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
        return \XLite\View\FormField\Select\TestLiveMode::TEST === $method->getSetting('mode');
    }

    /**
     * Get warning note by payment method
     *
     * @param \XLite\Model\Payment\Method $method Payment method
     *
     * @return string
     */
    public function getWarningNote(\XLite\Model\Payment\Method $method)
    {
        return null;
    }

    /**
     * Check - payment method is forced enabled or not
     *
     * @param \XLite\Model\Payment\Method $method Payment method
     *
     * @return boolean
     */
    public function isForcedEnabled(\XLite\Model\Payment\Method $method)
    {
        return false;
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
        return null;
    }

    /**
     * Check - payment method can be enabled or not
     *
     * @param \XLite\Model\Payment\Method $method Payment method
     *
     * @return boolean
     */
    public function canEnable(\XLite\Model\Payment\Method $method)
    {
        return $this->isConfigured($method);
    }

    /**
     * Get note with explanation why payment method can not be enabled
     *
     * @param \XLite\Model\Payment\Method $method Payment method
     *
     * @return string
     */
    public function getForbidEnableNote(\XLite\Model\Payment\Method $method)
    {
        return null;
    }

    /**
     * Get links
     *
     * @param \XLite\Model\Payment\Method $method Payment method
     *
     * @return array
     */
    public function getLinks(\XLite\Model\Payment\Method $method)
    {
        return array();
    }

    /**
     * Get URL of referral page
     *
     * @return string
     */
    public function getReferralPageURL(\XLite\Model\Payment\Method $method)
    {
        return null;
    }

    /**
     * Return true if payment method settings form should use default submit button.
     * Otherwise, settings widget must define its own button
     *
     * @return boolean
     */
    public function useDefaultSettingsFormButton()
    {
        return true;
    }

    /**
     * Do something when payment method is enabled or disabled
     * Check $method->getEnabled() to test this flag
     *
     * @return void
     */
    public function enableMethod(\XLite\Model\Payment\Method $method)
    {
        return null;
    }

    /**
     * Return true if processor is for COD payment method
     *
     * @return boolean
     */
    public function isCOD()
    {
        return false;
    }

    /**
     * Return true if payment method selection require to update input payment template
     *
     * @param \XLite\Model\Payment\Method $method Payment method
     *
     * @return boolean
     */
    public function isCheckoutUpdateActionRequired(\XLite\Model\Payment\Method $method)
    {
        return $this->getInputTemplate();
    }

    // }}}

    // {{{ Primary data fields

    /**
     * Get list of primary transaction data fields with values
     * These data are displayed on the order page, invoice and packing slip
     *
     * @param \XLite\Model\Payment\Transaction $transaction Payment transaction
     * @param boolean                          $onlyPrimary Flag: true - return only primary fields OPTIONAL
     *
     * @return array
     */
    public function getTransactionData($transaction, $onlyPrimary = false)
    {
        $result = array();

        $primaryDataFields = $this->getPrimaryInputDataFields();

        if (!$onlyPrimary || $primaryDataFields) {

            $labels = $this->getInputDataLabels();

            foreach ($transaction->getTransactionData(true) as $data) {

                if (
                    $data->isAvailable()
                    && isset($labels[$data->getName()])
                    && (
                        !$onlyPrimary
                        || in_array($data->getName(), $primaryDataFields)
                    )
                ) {
                    $result[] = array(
                        'name'  => $data->getName(),
                        'title' => !empty($labels[$data->getName()]) ? $labels[$data->getName()] : $data->getName(),
                        'value' => $data->getValue(),
                    );
                }
            }
        }

        return $result;
    }

    /**
     * Get list of primary input fields
     *
     * @return array
     */
    protected function getPrimaryInputDataFields()
    {
        return array();
    }

    // }}}
}
