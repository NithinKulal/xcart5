<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * X-Cart
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the software license agreement
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.x-cart.com/license-agreement.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to licensing@x-cart.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not modify this file if you wish to upgrade X-Cart to newer versions
 * in the future. If you wish to customize X-Cart for your needs please
 * refer to http://www.x-cart.com/ for more information.
 *
 * @category  X-Cart 5
 * @author    Qualiteam software Ltd <info@x-cart.com>
 * @copyright Copyright (c) 2011-2016 Qualiteam software Ltd <info@x-cart.com>. All rights reserved
 * @license   http://www.x-cart.com/license-agreement.html X-Cart 5 License Agreement
 * @link      http://www.x-cart.com/
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
