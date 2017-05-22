<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal;

/**
 * Paypal module
 */
abstract class Main extends \XLite\Module\AModule
{
    /**
     * Paypal methods service names
     */
    const PP_METHOD_PPA  = 'PaypalAdvanced';
    const PP_METHOD_PFL  = 'PayflowLink';
    const PP_METHOD_PFTR = 'PayflowTransparentRedirect';
    const PP_METHOD_EC   = 'ExpressCheckout';
    const PP_METHOD_PPS  = 'PaypalWPS';
    const PP_METHOD_PC   = 'PaypalCredit';
    const PP_METHOD_PAD  = 'PaypalAdaptive';

    /**
     * RESTAPI instance
     *
     * @var \XLite\Module\CDev\Paypal\Core\RESTAPI
     */
    protected static $RESTAPI;

    /**
     * Payment methods
     *
     * @var \XLite\Model\Payment\Method[]
     */
    protected static $paymentMethod = array();

    /**
     * Author name
     *
     * @return string
     */
    public static function getAuthorName()
    {
        return 'X-Cart team';
    }

    /**
     * Module name
     *
     * @return string
     */
    public static function getModuleName()
    {
        return 'PayPal';
    }

    /**
     * Module major version
     *
     * @return string
     */
    public static function getMajorVersion()
    {
        return '5.3';
    }

    /**
     * Module version
     *
     * @return string
     */
    public static function getMinorVersion()
    {
        return '3';
    }

    /**
     * Get module build number (4th number in the version)
     *
     * @return string
     */
    public static function getBuildVersion()
    {
        return '7';
    }

    /**
     * Module description
     *
     * @return string
     */
    public static function getDescription()
    {
        return 'Enables taking payments for your online store via PayPal services.';
    }

    /**
     * Return link to settings form
     *
     * @return string
     */
    public static function getSettingsForm()
    {
        return null;
    }

    /**
     * Add record to the module log file
     *
     * @param string $message Text message OPTIONAL
     * @param mixed  $data    Data (can be any type) OPTIONAL
     *
     * @return void
     */
    public static function addLog($message = null, $data = null)
    {
        if ($message && $data) {
            $msg = array(
                'message' => $message,
                'data'    => $data,
            );

        } else {
            $msg = ($message ?: ($data ?: null));
        }

        if (!is_string($msg)) {
            $msg = var_export($msg, true);
        }

        \XLite\Logger::logCustom(
            self::getModuleName(),
            $msg
        );
    }

    /**
     * Returns payment method
     *
     * @param string  $serviceName Service name
     * @param boolean $enabled     Enabled status OPTIONAL
     *
     * @return \XLite\Model\Payment\Method
     */
    public static function getPaymentMethod($serviceName, $enabled = null)
    {
        if (!isset(static::$paymentMethod[$serviceName])) {
            static::$paymentMethod[$serviceName] = \XLite\Core\Database::getRepo('XLite\Model\Payment\Method')
                ->findOneBy(array('service_name' => $serviceName));
            if (!static::$paymentMethod[$serviceName]) {
                static::$paymentMethod[$serviceName] = false;
            }
        }
        return static::$paymentMethod[$serviceName]
            && (
                is_null($enabled)
                || static::$paymentMethod[$serviceName]->getEnabled() === (bool) $enabled
            )
            ? static::$paymentMethod[$serviceName]
            : null;
    }

    /**
     * Returns true if ExpressCheckout payment is enabled
     *
     * @param \XLite\Model\Cart $order Cart object OPTIONAL
     *
     * @return boolean
     */
    public static function isExpressCheckoutEnabled($order = null)
    {
        static $result;

        $index = (null !== $order) ? 1 : 0;

        if (!isset($result[$index])) {
            $paymentMethod = static::getPaymentMethod(static::PP_METHOD_EC, true);
            $result[$index] = $paymentMethod && $paymentMethod->isEnabled();

            if ($order && $result[$index]) {
                $result[$index] = $paymentMethod->getProcessor()->isApplicable($order, $paymentMethod);
            }
        }

        return $result[$index];
    }

    /**
     * Check if In-Context checkout available
     *
     * @return boolean
     */
    public static function isInContextCheckoutAvailable()
    {
        static $result;

        if (null === $result) {
            // https://developer.paypal.com/docs/classic/express-checkout/in-context/#eligibility-review
            $allowedCountries = array(
                'US', 'GB', 'FR', 'DE', 'AU',
                'CA', 'IT', 'ES', 'AT', 'BE',
                'DK', 'NO', 'NL', 'PL', 'SE',
                'CH', 'TR'
            );
            $allowedCurrencies = array(
                'USD', 'EUR', 'GBP', 'CAD', 'AUD',
                'DKK', 'NOK', 'PLN', 'SEK', 'CHF',
                'TRY'
            );

            /** @var \XLite\Model\Cart $cart */
            $cart = \XLite\Model\Cart::getInstance();
            $currency = $cart->getCurrency()->getCode();

            /** @var \XLite\Model\Address $billingAddress */
            $billingAddress = $cart->getProfile()
                ? $cart->getProfile()->getBillingAddress()
                : null;

            $customerCountry = $billingAddress
                ? $billingAddress->getCountryCode()
                : null;

            $result = in_array($currency, $allowedCurrencies, true)
                && (null === $customerCountry || in_array($customerCountry, $allowedCountries, true))
                && static::getMerchantId();
        }

        return $result;
    }

    /**
     * Returns Merchant Id for In-Context Checkout
     *
     * @return string
     */
    public static function getMerchantId()
    {
        $paymentMethod = static::getPaymentMethod(static::PP_METHOD_EC, true);

        return $paymentMethod
            ? $paymentMethod->getSetting('merchantId')
            : '';
    }

    /**
     * Returns BuyNow button availability status
     *
     * @return boolean
     */
    public static function isBuyNowEnabled()
    {
        static $result;

        if (null === $result) {
            $paymentMethod = static::getPaymentMethod(static::PP_METHOD_EC, true);

            $result = (bool) $paymentMethod->getSetting('buyNowEnabled');
        }

        return $result;
    }

    /**
     * Returns true if PaypalCredit payment is enabled
     *
     * @param \XLite\Model\Cart $order Cart object OPTIONAL
     *
     * @return boolean
     */
    public static function isPaypalCreditEnabled($order = null)
    {
        static $result;

        $index = (null !== $order ? 1 : 0);

        if (!isset($result[$index])) {
            $paymentMethod = static::getPaymentMethod(static::PP_METHOD_PC, true);
            $result[$index] = $paymentMethod
                && $paymentMethod->isEnabled()
                && $paymentMethod->getSetting('enabled')
                && static::isExpressCheckoutEnabled($order);
        }

        return $result[$index];
    }

    /**
     * Returns true if PaypalWPS payment is enabled
     *
     * @param \XLite\Model\Cart $order Cart object OPTIONAL
     *
     * @return boolean
     */
    public static function isPaypalWPSEnabled($order = null)
    {
        static $result;

        $index = (null !== $order) ? 1 : 0;

        if (!isset($result[$index])) {
            $paymentMethod = static::getPaymentMethod(static::PP_METHOD_PPS, true);
            $result[$index] = $paymentMethod && $paymentMethod->isEnabled();
        }

        return $result[$index];
    }

    /**
     * Returns true if PaypalAdaptive payment is enabled
     *
     * @param \XLite\Model\Cart $order Cart object OPTIONAL
     *
     * @return boolean
     */
    public static function isPaypalAdaptiveEnabled($order = null)
    {
        static $result;

        $index = (null !== $order) ? 1 : 0;

        if (!isset($result[$index])) {
            $paymentMethod = static::getPaymentMethod(static::PP_METHOD_PAD, true);
            $result[$index] = $paymentMethod && $paymentMethod->isEnabled();
        }

        return $result[$index];
    }

    /**
     * Return list of mutually exclusive modules
     *
     * @return array
     */
    public static function getMutualModulesList()
    {
        $list = parent::getMutualModulesList();
        $list[] = 'CDev\PaypalWPS';

        return $list;
    }

    /**
     * The module is defined as the payment module
     *
     * @return integer
     */
    public static function getModuleType()
    {
        return static::MODULE_TYPE_PAYMENT;
    }

    /**
     * Get logo
     *
     * @return string
     */
    public static function getLogo()
    {
        return \XLite\Core\URLManager::getShopURL(
            \XLite\Core\Layout::getInstance()->getLogo(),
            true,
            array(),
            \XLite\Core\URLManager::URL_OUTPUT_FULL,
            false
        );
    }

    /**
     * Get logo
     *
     * @return string
     */
    public static function getSignUpLogo()
    {
        $logo = \XLite\Core\Layout::getInstance()->getResourceWebPath(
            'modules/CDev/Paypal/signup_logo.png',
            \XLite\Core\Layout::WEB_PATH_OUTPUT_URL,
            \XLite::ADMIN_INTERFACE
        );

        return \XLite\Core\URLManager::getShopURL(
            $logo,
            true,
            array(),
            \XLite\Core\URLManager::URL_OUTPUT_FULL,
            false
        );
    }

    /**
     * Return RESTAPI instance
     *
     * @return \XLite\Module\CDev\Paypal\Core\RESTAPI
     */
    public static function getRESTAPIInstance()
    {
        if (null === static::$RESTAPI) {
            static::$RESTAPI = new \XLite\Module\CDev\Paypal\Core\RESTAPI();
        }

        return static::$RESTAPI;
    }

    /**
     * Returns paypal methods service codes
     *
     * @return array
     */
    public static function getServiceCodes()
    {
        return array(
            static::PP_METHOD_PPA,
            static::PP_METHOD_PFL,
            static::PP_METHOD_EC,
            static::PP_METHOD_PPS,
            static::PP_METHOD_PC,
            static::PP_METHOD_PAD,
        );
    }
}
