<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\Model\Payment\Processor;

use \XLite\Module\CDev\Paypal;

/**
 * Paypal Payments Standard payment processor
 *
 * @see https://developer.paypal.com/docs/classic/paypal-payments-standard/integration-guide/Appx_websitestandard_htmlvariables
 */
class PaypalWPS extends \XLite\Model\Payment\Base\WebBased
{
    /**
     * Referral page URL
     *
     * @var string
     */
    protected $referralPageURL = 'https://www.paypal.com/webapps/mpp/referral/paypal-payments-standard?partner_id=XCART5_Cart';

    /**
     * Knowledge base page URL
     *
     * @var string
     */
    protected $knowledgeBasePageURL = 'http://kb.x-cart.com/en/payments/paypal/setting_up_paypal_payments_standard.html';

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->api = new Paypal\Core\PayflowAPI();

        $method = Paypal\Main::getPaymentMethod(
            Paypal\Main::PP_METHOD_PPS
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
     * Get knowledge base page URL
     *
     * @return string
     */
    public function getKnowledgeBasePageURL()
    {
        return $this->knowledgeBasePageURL;
    }

    /**
     * Prevent enabling Paypal Standard if Paypal Advanced is already enabled
     *
     * @param \XLite\Model\Payment\Method $method Payment method object
     *
     * @return boolean
     */
    public function canEnable(\XLite\Model\Payment\Method $method)
    {
        return parent::canEnable($method)
            && Paypal\Main::PP_METHOD_PPS == $method->getServiceName()
            && !$this->isPaypalAdvancedEnabled();
    }

    /**
     * Return true if ExpressCheckout method is enabled
     *
     * @return boolean
     */
    public function isExpressCheckoutEnabled()
    {
        static $result = null;

        if (!isset($result)) {
            $result = $this->isPaypalMethodEnabled(Paypal\Main::PP_METHOD_EC);
        }

        return $result;
    }

    /**
     * Return true if Paypal Advanced method is enabled
     *
     * @return boolean
     */
    public function isPaypalAdvancedEnabled()
    {
        static $result = null;

        if (!isset($result)) {
            $result = $this->isPaypalMethodEnabled(Paypal\Main::PP_METHOD_PPA);
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

        if (Paypal\Main::PP_METHOD_PPS == $method->getServiceName()) {
            $result = 'This payment method cannot be enabled together with PayPal Advanced method';
        }

        return $result;
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

        if (Paypal\Model\Payment\Processor\PaypalIPN::getInstance()->isCallbackIPN()) {
            Paypal\Model\Payment\Processor\PaypalIPN::getInstance()
                ->tryProcessCallbackIPN($transaction, $this);
        }

        $this->saveDataFromRequest();
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
            if ($this->api->isTransactionCancellable($transaction)) {
                $this->setDetail(
                    'cancel',
                    'Customer has canceled checkout before completing their payments'
                );
                $this->transaction->setStatus($transaction::STATUS_CANCELED);
            }

        } elseif ($transaction::STATUS_INPROGRESS == $this->transaction->getStatus()) {
            $this->transaction->setStatus($transaction::STATUS_PENDING);
        }
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
            && $method->getSetting('account');
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
     * Return TRUE if the test mode is ON
     *
     * @param \XLite\Model\Payment\Method $method Payment method object
     *
     * @return boolean
     */
    public function isTestMode(\XLite\Model\Payment\Method $method)
    {
        return \XLite\View\FormField\Select\TestLiveMode::TEST === $method->getSetting('mode');
    }


    /**
     * Return true if "serviceName" method is enabled
     *
     * @param string $serviceName Service name
     *
     * @return boolean
     */
    protected function isPaypalMethodEnabled($serviceName)
    {
        $method = Paypal\Main::getPaymentMethod($serviceName);

        return $method && $method->isEnabled();
    }

    /**
     * Get redirect form URL
     *
     * @return string
     */
    protected function getFormURL()
    {
        return $this->isTestMode($this->transaction->getPaymentMethod())
            ? 'https://www.sandbox.paypal.com/cgi-bin/webscr'
            : 'https://www.paypal.com/cgi-bin/webscr';
    }

    /**
     * Return ITEM NAME for request
     *
     * @return string
     */
    protected function getItemName()
    {
        return $this->getSetting('description') . '(Order #' . $this->getTransactionId() . ')';
    }

    /**
     * Returns order items
     *
     * @param \XLite\Model\Order $order Order
     *
     * @return array
     * @see    https://developer.paypal.com/docs/classic/paypal-payments-standard/integration-guide/Appx_websitestandard_htmlvariables
     */
    protected function getItems($order)
    {
        $result = array();

        $itemsSubtotal  = 0;

        if ($order->countItems()) {
            $index = 1;

            /** @var \XLite\Model\Currency $currency */
            $currency = $order->getCurrency();

            foreach ($order->getItems() as $item) {
                $amt = $currency->roundValue($item->getItemNetPrice());
                $result['amount_' . $index] = $amt;

                /** @var \XLite\Model\Product $product */
                $product = $item->getProduct();
                $result['item_name_' . $index] = $product->getName();

                $qty = $item->getAmount();
                $result['quantity_' . $index] = $qty;
                $itemsSubtotal += $amt * $qty;
                ++$index;
            }

            // Prepare data about discount

            $discount = $currency->roundValue(
                $order->getSurchargeSumByType(\XLite\Model\Base\Surcharge::TYPE_DISCOUNT)
            );

            if (0 != $discount) {
                $result['discount_amount_cart']  = $discount;
            }

            $result = array_merge($result, array('items_amount' => $itemsSubtotal));

            // Prepare data about summary tax cost

            $taxCost = $currency->roundValue(
                $order->getSurchargeSumByType(\XLite\Model\Base\Surcharge::TYPE_TAX)
            );

            if (0 < $taxCost) {
                $result['tax_cart'] = $taxCost;
            }
        }

        return $result;
    }

    /**
     * Get redirect form fields list
     *
     * @return array
     * @see    https://developer.paypal.com/docs/classic/paypal-payments-standard/integration-guide/Appx_websitestandard_htmlvariables
     */
    protected function getFormFields()
    {
        /** @var \XLite\Model\Order $order */
        $order = $this->getOrder();

        /** @var \XLite\Model\Currency $currency */
        $currency = $order->getCurrency();

        $orderTotal = $currency->roundValue($order->getTotal());
        $orderNumber = $this->getTransactionId($this->getSetting('prefix'));

        $shippingCost = $this->getShippingCost($order);

        $params = array(
            'return'        => $this->getReturnURL(null, true),
            'cancel_return' => $this->getReturnURL(null, true, true),
            'shopping_url'  => $this->getReturnURL(null, true, true),
            'notify_url'    => $this->getCallbackURL(null, true),
            'rm'            => '2',
            'bn'            => 'XCART5_Cart',
            'upload'        => 1,

            'charset'       => 'UTF-8',
            'cmd'           => '_cart',
            'redirect_cmd'  => '_xclick',
            'business'      => $this->getSetting('account'),

            'custom'        => $order->getOrderId(),
            'invoice'       => $orderNumber,

            'currency_code' => $currency->getCode(),

            'shipping_1'    => (float) $shippingCost,
        );

        if (\XLite\Core\Config::getInstance()->Security->customer_security) {
            $fields['cpp_header_image'] = Paypal\Main::getLogo();
        }

        $items = $this->getItems($order);

        // To avoid total mismatch clear tax and shipping cost
        $taxAmt = isset($items['tax_cart']) ? $items['tax_cart'] : 0;
        if (abs($orderTotal - $items['items_amount'] - $taxAmt - (float) $shippingCost) <= 0.0000000001) {
            unset($items['items_amount']);
            $params = array_merge($params, $items);

        } else {
            $params['cmd'] = '_ext-enter';
            $params['amount'] = $orderTotal;
            $params['item_name'] = $this->getItemName();
            unset($params['shipping_1']);
        }

        $profile = $this->getProfile();

        $params = array_merge(
            $params,
            array(
                'address_override' => 1,
                'email'            => $profile->getLogin(),
            )
        );

        if (null !== $shippingCost) {
            /** @var \XLite\Model\Address $address */
            $address = $profile->getShippingAddress();

            $params = array_merge(
                $params,
                array(
                    'first_name'    => $address->getFirstname(),
                    'last_name'     => $address->getLastname(),
                    'country'       => $this->getCountryFieldValue(),
                    'state'         => $this->getStateFieldValue(),
                    'address1'      => $address->getStreet(),
                    'address2'      => 'n/a',
                    'city'          => $address->getCity(),
                    'zip'           => $address->getZipcode(),
                )
            );
        }

        $params = array_merge($params, $this->getPhone());

        return array_filter($params, function ($item) {
            return trim($item) !== '';
        });
    }

    /**
     * Return amount value. Specific for Paypal
     *
     * @return string
     */
    protected function getAmountValue()
    {
        $value = $this->transaction->getValue();

        settype($value, 'float');

        $value = sprintf('%0.2f', $value);

        return $value;
    }

    /**
     * Return Country field value. if no country defined we should use '' value
     *
     * @return string
     */
    protected function getCountryFieldValue()
    {
        $address = $this->getProfile()->getShippingAddress();

        return $address->getCountry()
            ? $address->getCountry()->getCode()
            : '';
    }

    /**
     * Return State field value. If country is US then state code must be used.
     *
     * @return string
     */
    protected function getStateFieldValue()
    {
        $address = $this->getProfile()->getShippingAddress();

        return 'US' === $this->getCountryFieldValue()
            ? $address->getState()->getCode()
            : $address->getState()->getState();
    }

    /**
     * Get shipping cost for set express checkout
     *
     * @param \XLite\Model\Order $order Order
     *
     * @return float|null
     */
    protected function getShippingCost($order)
    {
        $result = null;

        $shippingModifier = $order->getModifier(\XLite\Model\Base\Surcharge::TYPE_SHIPPING, 'SHIPPING');

        if ($shippingModifier && $shippingModifier->canApply()) {
            /** @var \XLite\Model\Currency $currency */
            $currency = $order->getCurrency();

            $result = (float) $currency->roundValue(
                $order->getSurchargeSumByType(\XLite\Model\Base\Surcharge::TYPE_SHIPPING)
            );
        }

        return $result;
    }

    /**
     * Return Phone structure. specific for Paypal
     *
     * @return array
     */
    protected function getPhone()
    {
        $result = array();

        $phone = $this->getProfile()->getBillingAddress()->getPhone();

        $phone = preg_replace('![^\d]+!', '', $phone);

        if ($phone) {
            if ($this->getProfile()->getBillingAddress()->getCountry()
                && 'US' == $this->getProfile()->getBillingAddress()->getCountry()->getCode()
            ) {
                $result = array(
                    'night_phone_a' => substr($phone, -10, -7),
                    'night_phone_b' => substr($phone, -7, -4),
                    'night_phone_c' => substr($phone, -4),
                );
            } else {
                $result['night_phone_b'] = substr($phone, -10);
            }
        }

        return $result;
    }

    /**
     * Define saved into transaction data schema
     *
     * @return array
     */
    protected function defineSavedData()
    {
        return array(
            'secureid'          => 'Transaction id',
            'mc_gross'          => 'Payment amount',
            'payment_type'      => 'Payment type',
            'payment_status'    => 'Payment status',
            'pending_reason'    => 'Pending reason',
            'reason_code'       => 'Reason code',
            'mc_currency'       => 'Payment currency',
            'auth_id'           => 'Authorization identification number',
            'auth_status'       => 'Status of authorization',
            'auth_exp'          => 'Authorization expiration date and time',
            'auth_amount'       => 'Authorization amount',
            'payer_id'          => 'Unique customer ID',
            'payer_email'       => 'Customer\'s primary email address',
            'txn_id'            => 'Original transaction identification number',
        );
    }

    /**
     * Log redirect form
     *
     * @param array $list Form fields list
     *
     * @return void
     */
    protected function logRedirect(array $list)
    {
        $list = $this->maskCell($list, 'account');

        parent::logRedirect($list);
    }

    /**
     * Get allowed currencies
     * https://developer.paypal.com/docs/classic/api/currency_codes/
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
                'AUD', 'BRL', 'CAD', 'CZK', 'DKK',
                'EUR', 'HKD', 'HUF', 'ILS', 'JPY',
                'MYR', 'MXN', 'NOK', 'NZD', 'PHP',
                'PLN', 'GBP', 'RUB', 'SGD', 'SEK',
                'CHF', 'TWD', 'THB', 'TRY', 'USD',
            )
        );
    }
}
