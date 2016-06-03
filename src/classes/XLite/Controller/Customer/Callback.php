<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Customer;

/**
 * Payment method callback
 */
class Callback extends \XLite\Controller\Customer\ACheckoutReturn
{
    /**
     * Payment transaction cache
     *
     * @var \XLite\Model\Payment\Transaction
     */
    protected $transaction;

    /**
     * Hard-coded value to prevent the doAction{action}() calls if the request goes with the "action" parameter
     *
     * @return string
     */
    public function getAction()
    {
        return 'callback';
    }

    /**
     * Define the detection method to check the ownership of the transaction
     *
     * @return string
     */
    protected function getDetectionMethodName()
    {
        return 'getCallbackOwnerTransaction';
    }

    /**
     * Stub for the CMS connectors
     *
     * @return boolean
     */
    protected function checkStorefrontAccessibility()
    {
        return \XLite\Core\Auth::getInstance()->isAccessibleStorefront()
            || \XLite::getInstance()->getOptions(
                    array('storefront_options', 'callback_opened')
                );
    }

    /**
     * Process callback
     *
     * @return void
     */
    protected function doActionCallback()
    {
        $transaction = $this->detectTransaction();

        if ($transaction) {
            $this->transaction = $transaction;

            $transaction->getPaymentMethod()->getProcessor()->processCallback($transaction);

            $cart = $transaction->getOrder();
            if ($cart instanceof \XLite\Model\Cart) {
                $cart->tryClose();
            }

            $transaction->getOrder()->setPaymentStatusByTransaction($transaction);
            $transaction->getOrder()->update();

            \XLite\Core\Database::getEM()->flush();

        } else {
            \XLite\Logger::getInstance()->log(
                'Request callback with undefined payment transaction' . PHP_EOL
                . 'Data: ' . var_export(\XLite\Core\Request::getInstance()->getData(), true),
                LOG_ERR
            );
        }

        $this->set('silent', true);
    }

    /**
     * Check - is service controller or not
     *
     * @return boolean
     */
    protected function isServiceController()
    {
        return true;
    }
}
