<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\Controller\Customer;

use \XLite\Module\CDev\Paypal;

/**
 * Checkout controller
 */
class Checkout extends \XLite\Controller\Customer\Checkout implements \XLite\Base\IDecorator
{
    /**
     * Define the actions with no secure token
     *
     * @return array
     */
    public static function defineFreeFormIdActions()
    {
        return array_merge(
            parent::defineFreeFormIdActions(),
            array(
                'express_checkout_return'
            )
        );
    }

    /**
     * Get page title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->isReturnedAfterExpressCheckout()
            ? static::t('Review & Submit order')
            : parent::getTitle();
    }

    /**
     * Check if customer is returned from PP EC
     *
     * @return boolean
     */
    public function isReturnedAfterExpressCheckout()
    {
        return \XLite\Core\Request::getInstance()->ec_returned === '1';
    }

    /**
     * Order placement is success
     *
     * @param boolean $fullProcess Full process or not OPTIONAL
     *
     * @return void
     */
    public function processSucceed($fullProcess = true)
    {
        parent::processSucceed($fullProcess);

        if (\XLite\Core\Request::getInstance()->inContext) {
            \XLite\Core\Session::getInstance()->inContextRedirect = true;
            \XLite\Core\Session::getInstance()->cancelUrl = \XLite\Core\Request::getInstance()->cancelUrl;
        }
    }

    /**
     * Set order note
     * @unused
     *
     * @return void
     */
    public function doActionSetOrderNote()
    {
        if (isset(\XLite\Core\Request::getInstance()->notes)) {
            $this->getCart()->setNotes(\XLite\Core\Request::getInstance()->notes);
        }
        \XLite\Core\Database::getEM()->flush();
        exit();
    }

    /**
     * doActionStartExpressCheckout
     *
     * @return void
     */
    protected function doActionStartExpressCheckout()
    {
        if (Paypal\Main::isExpressCheckoutEnabled()) {
            $paymentMethod = $this->getExpressCheckoutPaymentMethod();

            $this->getCart()->setPaymentMethod($paymentMethod);

            $this->updateCart();

            \XLite\Core\Session::getInstance()->ec_type
                = Paypal\Model\Payment\Processor\ExpressCheckout::EC_TYPE_SHORTCUT;

            $processor = $paymentMethod->getProcessor();
            $token = $processor->doSetExpressCheckout($paymentMethod);

            if (null !== $token) {
                \XLite\Core\Session::getInstance()->ec_token = $token;
                \XLite\Core\Session::getInstance()->ec_date = \XLite\Core\Converter::time();
                \XLite\Core\Session::getInstance()->ec_payer_id = null;
                \XLite\Core\Session::getInstance()->ec_ignore_checkout = (bool)\XLite\Core\Request::getInstance()->ignoreCheckout;

                if ($this->isAJAX()) {
                    \XLite\Core\Event::PayPalToken(array('token' => $processor->getExpressCheckoutRedirectURL($token)));

                } else {
                    $processor->redirectToPaypal($token);
                    exit;
                }


            } else {
                if (\XLite\Core\Request::getInstance()->inContext) {
                    \XLite\Core\Session::getInstance()->cancelUrl = \XLite\Core\Request::getInstance()->cancelUrl;
                    \XLite\Core\Session::getInstance()->inContextRedirect = true;
                    $this->setReturnURL($this->buildURL('checkout_failed'));
                }

                \XLite\Core\TopMessage::addError(
                    $processor->getErrorMessage() ?: 'Failure to redirect to PayPal.'
                );

                if ($this->isAJAX()) {
                    \XLite\Core\Event::PayPalToken(array('token' => ''));
                }
            }
        }
    }

    /**
     * doExpressCheckoutReturn
     *
     * @return void
     */
    protected function doActionExpressCheckoutReturn()
    {
        $request = \XLite\Core\Request::getInstance();
        $cart = $this->getCart();

        Paypal\Main::addLog('doExpressCheckoutReturn()', $request->getData());

        $checkoutAction = false;

        if (isset($request->cancel)) {
            \XLite\Core\Session::getInstance()->ec_token = null;
            \XLite\Core\Session::getInstance()->ec_date = null;
            \XLite\Core\Session::getInstance()->ec_payer_id = null;
            \XLite\Core\Session::getInstance()->ec_type = null;

            $cart->unsetPaymentMethod();

            \XLite\Core\TopMessage::addWarning('Express Checkout process stopped.');

        } elseif (!isset($request->token) || $request->token !== \XLite\Core\Session::getInstance()->ec_token) {
            \XLite\Core\TopMessage::getInstance()->addError('Wrong token of Express Checkout. Please try again. If the problem persists, contact the administrator.');

        } elseif (!isset($request->PayerID)) {
            \XLite\Core\TopMessage::getInstance()->addError('PayerID value was not returned by PayPal. Please try again. If the problem persists, contact the administrator.');

        } else {
            // Express Checkout shortcut flow processing

            \XLite\Core\Session::getInstance()->ec_type
                = Paypal\Model\Payment\Processor\ExpressCheckout::EC_TYPE_SHORTCUT;

            \XLite\Core\Session::getInstance()->ec_payer_id = $request->PayerID;
            $paymentMethod = $this->getExpressCheckoutPaymentMethod();
            $processor = $paymentMethod->getProcessor();

            $buyerData = $processor->doGetExpressCheckoutDetails($paymentMethod, $request->token);

            if (empty($buyerData)) {
                \XLite\Core\TopMessage::getInstance()->addError('Your address data was not received from PayPal. Please try again. If the problem persists, contact the administrator.');

            } else {
                // Fill the cart with data received from Paypal
                $this->requestData = $this->prepareBuyerData($processor, $buyerData);

                if (!\XLite\Core\Auth::getInstance()->isLogged()) {
                    $this->updateProfile();
                }

                $modifier = $cart->getModifier(\XLite\Model\Base\Surcharge::TYPE_SHIPPING, 'SHIPPING');
                if ($modifier && $modifier->canApply()) {
                    $this->requestData['billingAddress'] = $this->requestData['shippingAddress'];
                    $this->requestData['same_address'] = true;

                    $this->updateShippingAddress();

                    $this->updateBillingAddress();
                }

                $this->setCheckoutAvailable();

                $this->updateCart();

                if (\XLite\Core\Session::getInstance()->ec_ignore_checkout) {
                    $this->doActionCheckout();
                } else {
                    $params = array(
                        'ec_returned'   => true
                    );
                    $this->setReturnURL($this->buildURL('checkout', '', $params));
                }
                \XLite\Core\Session::getInstance()->ec_ignore_checkout = null;

                $checkoutAction = true;
            }
        }

        if (!$checkoutAction) {
            $this->setReturnURL(\XLite\Core\Request::getInstance()->cancelUrl ?: $this->buildURL('checkout'));
        }
    }

    /**
     * Do payment
     *
     * @return void
     */
    protected function doPayment()
    {
        $this->setHardRedirect(
            $this->isReturnedAfterExpressCheckout()
        );

        parent::doPayment();
    }

    /**
     * Set up ec_type flag to 'mark' value if payment method selected on checkout
     *
     * @return void
     */
    protected function doActionPayment()
    {
        \XLite\Core\Session::getInstance()->ec_type
            = Paypal\Model\Payment\Processor\ExpressCheckout::EC_TYPE_MARK;

        parent::doActionPayment();
    }

    /**
     * Translate array of data received from Paypal to the array for updating cart
     *
     * @param \XLite\Model\Payment\Base\Processor $processor  Payment processor
     * @param array                               $paypalData Array of customer data received from Paypal
     *
     * @return array
     */
    protected function prepareBuyerData($processor, $paypalData)
    {
        $data = $processor->prepareBuyerData($paypalData);

        if (!\XLite\Core\Auth::getInstance()->isLogged()) {
            $data += array(
                'email' => str_replace(' ', '+', $paypalData['EMAIL']),
                'create_profile' => false,
            );
        }

        return $data;
    }

    /**
     * Get Express Checkout payment method
     *
     * @return \XLite\Model\Payment\Method
     */
    protected function getExpressCheckoutPaymentMethod()
    {
        $serviceName = \XLite\Core\Request::getInstance()->paypalCredit
            ? Paypal\Main::PP_METHOD_PC
            : Paypal\Main::PP_METHOD_EC;

        return Paypal\Main::getPaymentMethod($serviceName);
    }

    /**
     * Checkout
     * TODO: to revise
     *
     * @return void
     */
    protected function doActionCheckout()
    {
        parent::doActionCheckout();

        $cart = $this->getCart();
        $paymentMethods = $cart->getPaymentMethod();

        if ($paymentMethods) {
            $processor = $paymentMethods->getProcessor();

            if ($processor instanceof \XLite\Module\CDev\Paypal\Model\Payment\Processor\PayflowTransparentRedirect
                && $this->getReturnURL() === $this->buildURL('checkoutPayment')
            ) {
                $this->set('silent', true);
            }
        }
    }

    /**
     * Update profile
     *
     * @return void
     */
    protected function doActionUpdateProfile()
    {
        parent::doActionUpdateProfile();

        \XLite\Core\Event::updatePaypalTransparentRedirect(array());
    }

    /**
     * Return from payment gateway
     */
    protected function doActionReturn()
    {
        parent::doActionReturn();

        $order = \XLite\Model\Cart::getInstance(false);
        if (Paypal\Core\Lock\OrderLocker::getInstance()->isLocked($order)) {
            Paypal\Core\Lock\OrderLocker::getInstance()->unlock($order);
        }
    }
}
