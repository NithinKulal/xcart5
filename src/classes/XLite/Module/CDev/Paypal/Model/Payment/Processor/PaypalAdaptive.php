<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\Model\Payment\Processor;

use \XLite\Module\CDev\Paypal;

/**
 * Paypal Adaptive payment processor
 */
class PaypalAdaptive extends \XLite\Model\Payment\Base\WebBased
{
    /**
     * Referral page URL 
     * 
     * @var string
     */
    protected $referralPageURL = 'https://www.paypal.com/webapps/mpp/merchant';

    /**
     * Knowledge base page URL
     *
     * @var string
     */
    protected $knowledgeBasePageURL = 'https://developer.paypal.com/docs/classic/lifecycle/goingLive/#credentials';

    /**
     * Partner code
     *
     * @var string
     */
    protected static $partnerCode = 'XCart_AP';

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
     * Get knowledge base page URL
     *
     * @return array
     */
    public function getKnowledgeBasePageURLs()
    {
        return array(
            array(
                'name'  =>  static::t('Obtaining your live PayPal credentials'),
                'url'   =>  'https://developer.paypal.com/docs/classic/lifecycle/goingLive/#credentials',
            ),
            array(
                'name'  =>  static::t('Registering your application with PayPal'),
                'url'   =>  'https://developer.paypal.com/docs/classic/lifecycle/goingLive/#register',
            ),
        );
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
        return $this->referralPageURL;
    }

    /**
     * Get URL of help page
     *     *
     * @return string
     */
    public function getHelpFeesPageURL()
    {
        return 'https://developer.paypal.com/docs/classic/adaptive-payments/integration-guide/APIntro/#id091QF0N0MPF__id092SH0050HS';
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->api = new Paypal\Core\PaypalAdaptiveAPI();

        $method = Paypal\Main::getPaymentMethod(
            Paypal\Main::PP_METHOD_PAD
        );

        $this->api->setMethod($method);
        $this->api->setPartnerCode(static::$partnerCode);
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
     * Get allowed backend transactions
     *
     * @return string Status code
     */
    public function getAllowedTransactions()
    {
        return array(
            // Uncomment after #XCN-5553 implementation
            // \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_REFUND,
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
        return \XLite\Core\Converter::buildURL('paypal_settings', '', array('method_id' => $method->getMethodId()));
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
     * Get redirect form URL
     *
     * @return string
     */
    protected function getFormURL()
    {
        return $this->api->isTestMode()
            ? 'https://www.sandbox.paypal.com/cgi-bin/webscr'
            : 'https://www.paypal.com/cgi-bin/webscr';
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
     * Get form method
     *
     * @return string
     */
    protected function getFormMethod()
    {
        return static::FORM_METHOD_GET;
    }

    /**
     * Get redirect form fields list
     *
     * @return array
     * @see    https://developer.paypal.com/docs/classic/paypal-payments-standard/integration-guide/Appx_websitestandard_htmlvariables
     */
    protected function getFormFields()
    {
        $params = array(
            'cmd' => '_ap-payment',
        );

        $paypalAdaptiveResponse = $this->api->doPayCall(
            $this->getOrder(),
            $this->getReturnURL(null, true, true),  // Cancel
            $this->getReturnURL(null, true),        // Return
            $this->getCallbackURL(null, true)       // IPN Notification URL
        );

        if (isset($paypalAdaptiveResponse['payKey'])) {
            $params['paykey'] = $paypalAdaptiveResponse['payKey'];

            $setPaymentOptionsResponse = $this->api->doSetPaymentOptionsCall(
                $params['paykey']
            );
        }

        return $params;
    }

    /**
     * Define saved into transaction data schema
     *
     * @return array
     */
    protected function defineSavedData()
    {
        return array(
            'status'         => 'Status',
            'fees_payer'     => 'Fees payer',
            'sender_email'   => 'Customer\'s primary email address',
            'txnId'          => 'Original transaction identification number',
            'reason_code'    => 'Reason code',
            'trackingId'     => 'Tracking ID',
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
        parent::processReturn($transaction);

        if ($transaction->hasTtlForIpn()) {
            $transaction->removeTtlForIpn();
        }

        if (\XLite\Core\Request::getInstance()->cancel) {
            $this->setDetail(
                'cancel',
                'Customer has canceled checkout before completing their payments'
            );
            $this->transaction->setStatus($transaction::STATUS_CANCELED);

        } elseif ($transaction::STATUS_INPROGRESS == $this->transaction->getStatus()) {
            $this->transaction->setStatus($transaction::STATUS_PENDING);
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

        if (Paypal\Model\Payment\Processor\PaypalIPN::getInstance()->isCallbackAdaptiveIPN()) {
            Paypal\Model\Payment\Processor\PaypalIPN::getInstance()
                ->tryProcessCallbackIPN($transaction, $this);
        }

        $this->saveDataFromRequest();
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
            && $this->api->isConfigured()
            && Paypal\Main::PP_METHOD_PAD == $method->getServiceName()
            && \XLite\Core\Database::getRepo('XLite\Model\Module')->isModuleEnabled('XC\MultiVendor');
    }

    /**
     * Prevent enabling Paypal Adaptive if Multivendor is not installed and enabled
     *
     * @param \XLite\Model\Payment\Method $method Payment method object
     *
     * @return boolean
     */
    public function canEnable(\XLite\Model\Payment\Method $method)
    {
        return parent::canEnable($method)
            && Paypal\Main::PP_METHOD_PAD == $method->getServiceName()
            && \XLite\Core\Database::getRepo('XLite\Model\Module')->isModuleEnabled('XC\MultiVendor');
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
        $result = parent::getWarningNote($method);

        if (Paypal\Main::PP_METHOD_PAD === $method->getServiceName()
            && !\XLite\Core\Database::getRepo('XLite\Model\Module')->isModuleEnabled('XC\MultiVendor')
        ) {
            $result = static::t('To enable this payment method, you need Multi-vendor module installed.');
        }

        return $result;
    }

    /**
     * Multivendor must be enabled
     *
     * @param \XLite\Model\Order          $order  Order
     * @param \XLite\Model\Payment\Method $method Payment method
     *
     * @return boolean
     */
    public function isApplicable(\XLite\Model\Order $order, \XLite\Model\Payment\Method $method)
    {
        return $this->canEnable($method) && parent::isApplicable($order, $method);
    }
}
