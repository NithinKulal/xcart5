<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\Amazon\PayWithAmazon\Controller\Customer;

use XLite\Core\Auth;
use XLite\Core\Config;
use XLite\Core\Request;
use XLite\Module\Amazon\PayWithAmazon\Main;

/**
 * Amazon checkout controller
 */
class AmazonCheckout extends \XLite\Controller\Customer\Checkout
{
    /**
     * @param array $params
     */
    public function __construct(array $params)
    {
        $this->params = array_merge($this->params, ['orderReference']);

        parent::__construct($params);
    }

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Pay with Amazon');
    }

    /**
     * Return true if checkout layout is used
     *
     * @return boolean
     */
    public function isCheckoutLayout()
    {
        return true;
    }

    protected function doNoAction()
    {
        $cart          = $this->getCart();
        $paymentMethod = $cart->getPaymentMethod();
        $method        = Main::getMethod();

        if (null === $paymentMethod || $paymentMethod->getMethodId() !== $method->getMethodId()) {
            $profile = $cart->getProfile();
            if (null !== $profile) {
                $profile->setLastPaymentId($method->getMethodId());
            }

            $cart->setPaymentMethod($method);

            $this->updateCart();
        }

        parent::doNoAction();
    }

    protected function doActionCheckAddress()
    {
        $result = false;
        $client = Main::getClient();
        $orderReference = Request::getInstance()->orderReference;
        try {
            $response = $client->getOrderReferenceDetails([
                'amazon_order_reference_id' => $orderReference,
            ]);

            if ($response) {
                $processor = Main::getProcessor();

                $address = $processor->getAddressDataFromOrderReferenceDetails($response->toArray());
                if ($address) {
                    $this->updateAddress($address);

                    $result = true;
                }
            }
        } catch (\Exception $e) {
            $response = $e->getMessage();
        }

        if (!$result) {
            Main::log(
                [
                    'message' => 'Error: ' . __FUNCTION__,
                    'orefid'  => $orderReference,
                    'reply'   => $response,
                ]
            );
            echo 'error';
        } else {

            echo 'ok';
        }
    }

    /**
     * @param array $address
     */
    public function updateAddress($address)
    {
        $this->requestData['shippingAddress'] = $address;
        $this->requestData['billingAddress']  = $address;
        $this->requestData['same_address']    = true;

        $this->updateShippingAddress();
        $this->updateBillingAddress();
    }

    protected function doPayment()
    {
        parent::doPayment();

        if ($this->getCart()->isOpen()) {
            $processor = Main::getProcessor();
            if ($processor->invalidPaymentMethod && Auth::getInstance()->isLogged()) {
                $this->setReturnURL(
                    $this->buildURL(
                        'amazon_checkout',
                        '',
                        ['orderReference' => Request::getInstance()->orderReference]
                    )
                );
            } else {
                $this->setReturnURL($this->buildURL('cart'));
            }
        }
    }

    /**
     * Get 'Terms and conditions' page URL
     *
     * @return string
     */
    public function getTermsURL()
    {
        return Config::getInstance()->General->terms_url;
    }

    /**
     * Check if current page is accessible
     *
     * @return boolean
     */
    public function checkAccess()
    {
        return parent::checkAccess();
    }

    // workaround for compatibility with XP (XP's checkout/script.twig produce error without it)
    public function getXpcPaymentIds()
    {
        return [];
    }

    public function isCheckoutReady()
    {
        return false;
    }

    public function checkCheckoutAction()
    {
        return true;
    }

    public function getSaveCardBoxClass()
    {
        return '';
    }

    public function showSaveCardBox()
    {
        return false;
    }

    public function getXpcSavedCardPaymentId()
    {
        return false;
    }

    public function getXpcBillingAddressId()
    {
        return false;
    }

    public function isUseIframe()
    {
        return false;
    }
}
