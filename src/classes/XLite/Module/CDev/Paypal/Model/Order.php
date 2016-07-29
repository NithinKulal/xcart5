<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\Model;

/**
 * Order model
 */
class Order extends \XLite\Model\Order implements \XLite\Base\IDecorator
{
    /**
     * Exclude Express Checkout from the list of available for checkout payment methods
     * if Payflow Link or Paypal Advanced are avavilable
     *
     * @return array
     */
    public function getPaymentMethods()
    {
        $list = parent::getPaymentMethods();
        $transaction = $this->getFirstOpenPaymentTransaction();
        $paymentMethod = $transaction ? $transaction->getPaymentMethod() : null;

        if (null === $paymentMethod
            || (!$this->isExpressCheckout($paymentMethod)
                && !$this->isPaypalCredit($paymentMethod)
            )
        ) {
            $expressCheckoutKey = null;
            $found = false;

            foreach ($list as $k => $method) {
                if ($this->isExpressCheckout($method)) {
                    $expressCheckoutKey = $k;
                }

                if (in_array($method->getServiceName(), array('PayflowLink', 'PaypalAdvanced'), true)) {
                    $found = true;
                }

                if (null !== $expressCheckoutKey && $found) {
                    break;
                }
            }

            if (null !== $expressCheckoutKey && $found) {
                unset($list[$expressCheckoutKey]);
            }
        }

        $list = $this->sortPaypalMethods($list);

        return $list;
    }

    /**
     * Get only express checkout payment method
     *
     * @return array
     */
    public function getOnlyExpressCheckoutIfAvailable()
    {
        $list = parent::getPaymentMethods();

        $transaction = $this->getFirstOpenPaymentTransaction();

        $paymentMethod = $transaction ? $transaction->getPaymentMethod() : null;

        if (isset($paymentMethod) && $this->isExpressCheckout($paymentMethod) ) {
            $self = $this;
            // If customer return from Express checkout to confirm payment
            $list = array_filter($list, function($method) use ($self) {
                return $self->isExpressCheckout($method);
            });

        }

        return $list;
    }

    /**
     * Returns true if specified payment method is ExpressCheckout
     *
     * @param \XLite\Model\Payment\Method $method Payment method object
     *
     * @return boolean
     */
    public function isExpressCheckout($method)
    {
        return 'ExpressCheckout' === $method->getServiceName();
    }

    /**
     * Returns true if specified payment method is ExpressCheckout
     *
     * @param \XLite\Model\Payment\Method $method Payment method object
     *
     * @return boolean
     */
    public function isPaypalCredit($method)
    {
        return 'PaypalCredit' === $method->getServiceName();
    }

    /**
     * Returns the associative array of transaction IDs: PPREF and/or PNREF
     *
     * @return array
     */
    public function getTransactionIds()
    {
        $result = array();

        foreach ($this->getPaymentTransactions() as $t) {
            if ($this->isPaypalMethod($t->getPaymentMethod())) {
                $isTestMode = $t->getDataCell('test_mode');

                if (null !== $isTestMode) {
                    $result[] = array(
                        'url'   => '',
                        'name'  => 'Test mode',
                        'value' => 'yes',
                    );
                }

                $ppref = $t->getDataCell('PPREF');
                if (null !== $ppref) {
                    $result[] = array(
                        'url'   => $this->getTransactionIdURL($t, $ppref->getValue()),
                        'name'  => 'Unique PayPal transaction ID (PPREF)',
                        'value' => $ppref->getValue(),
                    );
                }

                $pnref = $t->getDataCell('PNREF');
                if (null !== $pnref) {
                    $result[] = array(
                        'url'   => '',
                        'name'  => 'Unique Payflow transaction ID (PNREF)',
                        'value' => $pnref->getValue(),
                    );
                }
            }
        }

        return $result;
    }

    /**
     * Place paypalCredit after paypalExpress
     *
     * @param array $paymentMethods Payment methods
     *
     * @return array
     */
    protected function sortPaypalMethods($paymentMethods)
    {
        $paypalCreditMethod = null;

        foreach ($paymentMethods as $key => $method) {
            if ($this->isPaypalCredit($method)) {
                $paypalCreditMethod = $method;
                unset($paymentMethods[$key]);
            }
        }


        if ($paypalCreditMethod) {
            $list = array();
            foreach ($paymentMethods as $method) {
                $list[] = $method;

                if ($this->isExpressCheckout($method)) {
                    $list[] = $paypalCreditMethod;
                }
            }

            $paymentMethods = $list;
        }

        return $paymentMethods;
    }



    /**
     * Get specific transaction URL on PayPal side
     *
     * @param \XLite\Model\Payment\Transaction $transaction Payment transaction object
     * @param string                           $id          Transaction ID (PPREF)
     *
     * @return string
     */
    protected function getTransactionIdURL($transaction, $id)
    {
        $isTestMode = $transaction->getDataCell('test_mode');

        return null !== $isTestMode
            ? 'https://www.sandbox.paypal.com/cgi-bin/webscr?cmd=_view-a-trans&id=' . $id
            : 'https://www.paypal.com/cgi-bin/webscr?cmd=_view-a-trans&id=' . $id;
    }

    /**
     * Return true if current payment method is PayPal
     *
     * @param \XLite\Model\Payment\Method $method Payment method object
     *
     * @return boolean
     */
    public function isPaypalMethod($method)
    {
        return null !== $method
            && in_array(
                $method->getServiceName(),
                array(
                    \XLite\Module\CDev\Paypal\Main::PP_METHOD_PPA,
                    \XLite\Module\CDev\Paypal\Main::PP_METHOD_PFL,
                    \XLite\Module\CDev\Paypal\Main::PP_METHOD_EC,
                    \XLite\Module\CDev\Paypal\Main::PP_METHOD_PPS,
                    \XLite\Module\CDev\Paypal\Main::PP_METHOD_PC,
                ),
                true
            );
    }
}
