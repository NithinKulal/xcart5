<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\XPaymentsConnector\Controller\Customer;

/**
 * Callback 
 *
 */
class Callback extends \XLite\Controller\Customer\Callback implements \XLite\Base\IDecorator
{

    /**
     * Allow check cart action
     *
     * @return string
     */
    public function getAction()
    {
        return 'check_cart' == \XLite\Core\Request::getInstance()->action
            ? \XLite\Core\Request::getInstance()->action
            : parent::getAction();
    }

    /**
     * Send current cart details back to X-Payments.   
     *
     * @return void
     */
    protected function doActionCheckCart()
    {
        $refId = \XLite\Core\Request::getInstance()->refId;
        
        $transaction = $this->detectTransaction();

        $xml = '';

        if ($transaction) {
            $cart = $transaction->getOrder();

            $response = array(
                'status' => 'cart-changed',
                'ref_id' => $refId,
            );

            $clientXpayments = \XLite\Module\CDev\XPaymentsConnector\Core\XPaymentsClient::getInstance();

            if (
                method_exists($transaction, 'isAntiFraudApplied')
                && method_exists($transaction, 'checkBlockOrder')
                && $transaction->isAntiFraudApplied()
                && $transaction->checkBlockOrder(true)
            ) {
                // ANTIFRAUD RELATED CHANGES

                // This makes a error top messsage at checkout
                $transaction->setDataCell('status', 'AF Error #1: Cannot process this order. Contact administrator', null, 'C');

            } else {

                // Prepare cart
                $preparedCart = $clientXpayments->prepareCart($cart, $transaction->getPaymentMethod(), $refId);

                if ($cart && $preparedCart) {
                    $response['cart'] = $preparedCart;
                }

            }

            try {

                // Convert array to XML and encrypt it
                $xml = $clientXpayments->encryptRequest($response);

            } catch (\XLite\Module\CDev\XPaymentsConnector\Core\XpcResponseException $exception) {

                // Doesn't matter, but al least we can send something
                $xml = $exception->getMessage();
            }

            print ($xml);
            die (0);
        }
    }

    /**
     * Process callback
     *
     * @return void
     */
    protected function doActionCallback()
    {
        $transaction = $this->detectTransaction();
        $xpcOrderCreateProfile = false;
        if (
            $transaction
            && $transaction->isXpc(false)
            && $transaction->getDataCell('xpc_session_id')
        ) {

            \XLite\Core\Session::getInstance()->loadBySid($transaction->getDataCell('xpc_session_id')->getValue());

            if (\XLite\Core\Session::getInstance()->order_create_profile) {

                // Save profile created at checkout flag
                // It's removed from session in processSucceed()
                \XLite\Core\Session::getInstance()->xpc_order_create_profile = true;
                $xpcOrderCreateProfile = true;
            }
        }

        parent::doActionCallback();

        if ($xpcOrderCreateProfile) {
            // That original session was removed from the database when the user was logged in.
            // But the returning customer will use this session. So reset the ID of the session.
            \XLite\Core\Session::getInstance()->getModel()->setSid($transaction->getDataCell('xpc_session_id')->getValue());
            \XLite\Core\Database::getEM()->flush();
        }
    }

}
