<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\Amazon\PayWithAmazon\Controller\Customer;
use XLite\Module\Amazon\PayWithAmazon\AMZ as AMZ;

/**
 * Amazon checkout controller
 */
class AmazonCheckout extends \XLite\Controller\Customer\ACustomer
{
    /**
     * params
     *
     * @var string
     */
    protected $params = array('target', 'amz_pa_ref');

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */ 
    public function getTitle()
    {
        return 'Pay with Amazon';
    }

    /**
     * Controller marks the cart calculation.
     * We need cart recalculation on amazon checkout page, so return true
     *
     * @return boolean
     */
    protected function markCartCalculate()
    {
        return true;
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

    /**
     * Go to cart view if cart is empty
     *
     * @return void
     */
    public function handleRequest()
    {
        // check if it's IPN callback
        if (\XLite\Core\Request::getInstance()->isipn == 'Y') {
            $this->handleIPNCallback();
            exit;
        }

        if (!$this->getCart()->checkCart()) {
            $this->setHardRedirect();
            $this->setReturnURL($this->buildURL('cart'));
            $this->doRedirect();
        }

        if (\XLite\Core\Request::getInstance()->isPost()) {

            switch (\XLite\Core\Request::getInstance()->mode) {

            case 'check_address':
                $orefid = \XLite\Core\Request::getInstance()->orefid;

                $addr_set = false;
                $res = AMZ::func_amazon_pa_request('GetOrderReferenceDetails', array(
                    'AmazonOrderReferenceId' => $orefid
                ));

                if ($res) {
                    $res = AMZ::func_array_path($res, 'GetOrderReferenceDetailsResponse/GetOrderReferenceDetailsResult/OrderReferenceDetails/Destination/PhysicalDestination/0/#');
                    if ($res) {

                        $tmp = array();
                        $tmp['zipcode'] = $res['PostalCode'][0]['#'];
                        $tmp['country_code'] = $res['CountryCode'][0]['#'];
                        $tmp['city'] = $res['City'][0]['#'];

                        if ($_st = \XLite\Core\Database::getRepo('XLite\Model\State')->findOneByCountryAndState($tmp['country_code'], $res['StateOrRegion'][0]['#'])) {
                            $tmp['state_id'] = $_st->getStateId();
                        } elseif (!empty($res['StateOrRegion'][0]['#'])) {
                            $tmp['custom_state'] = $res['StateOrRegion'][0]['#'];
                        }

                        $this->updateAddress($tmp);
                        $addr_set = true;
                    }
                }
                if (!$addr_set) {
                    echo 'error';
                    AMZ::func_amazon_pa_error("check address error: orefid=$orefid reply=" . print_r($res, true));
                } else {
                    echo 'ok';
                }

                break;

            case 'place_order':

                $amazon_pa_orefid = \XLite\Core\Request::getInstance()->amazon_pa_orefid;
                $cart_total_cost = $this->getCart()->getTotal();
                $customer_notes = \XLite\Core\Request::getInstance()->notes;

                $payment_method_text = 'Pay with Amazon'; 

                $this->getCart()->assignOrderNumber();

                // SetOrderReferenceDetails
                $res = AMZ::func_amazon_pa_request('SetOrderReferenceDetails', array(
                    'AmazonOrderReferenceId' => $amazon_pa_orefid,
                    'OrderReferenceAttributes.OrderTotal.Amount' => $cart_total_cost,
                    'OrderReferenceAttributes.OrderTotal.CurrencyCode' => \XLite\Core\Config::getInstance()->Amazon->PayWithAmazon->amazon_pa_currency,
                    'OrderReferenceAttributes.PlatformId' => AMZ::AMAZON_PA_PLATFORM_ID,
                    'OrderReferenceAttributes.SellerNote' => '',
                    'OrderReferenceAttributes.SellerOrderAttributes.SellerOrderId' => $this->getCart()->getOrderNumber(),
                ));

                // ConfirmOrderReference
                $res = AMZ::func_amazon_pa_request('ConfirmOrderReference', array(
                    'AmazonOrderReferenceId' => $amazon_pa_orefid,
                ));

                // get more order details using GetOrderReferenceDetails after confirmation
                $res = AMZ::func_amazon_pa_request('GetOrderReferenceDetails', array(
                    'AmazonOrderReferenceId' => $amazon_pa_orefid,
                ));
                if ($res) {
                    $dest = AMZ::func_array_path($res, 'GetOrderReferenceDetailsResponse/GetOrderReferenceDetailsResult/OrderReferenceDetails/Destination/PhysicalDestination/0/#');
                    $buyer = AMZ::func_array_path($res, 'GetOrderReferenceDetailsResponse/GetOrderReferenceDetailsResult/OrderReferenceDetails/Buyer/0/#');
                    if ($dest) {
                        //address
                        $tmp = array();
                        $tmp['zipcode'] = $dest['PostalCode'][0]['#'];
                        $tmp['country_code'] = $dest['CountryCode'][0]['#'];
                        $tmp['city'] = $dest['City'][0]['#'];

                        if ($_st = \XLite\Core\Database::getRepo('XLite\Model\State')->findOneByCountryAndState($tmp['country_code'], $dest['StateOrRegion'][0]['#'])) {
                            $tmp['state_id'] = $_st->getStateId();
                        } elseif (!empty($dest['StateOrRegion'][0]['#'])) {
                            $tmp['custom_state'] = $dest['StateOrRegion'][0]['#'];
                        }

                        if (!empty($dest['Phone'][0]['#'])) {
                            $tmp['phone'] = $dest['Phone'][0]['#'];
                        }
                        $tmp['street'] = $dest['AddressLine1'][0]['#'];
                        if (isset($dest['AddressLine2'])) {
                            $tmp['street'] .= ' ' . $dest['AddressLine2'][0]['#'];
                        }

                        list($tmp['firstname'], $tmp['lastname']) = explode(' ', $dest['Name'][0]['#'], 2);
                        if (empty($tmp['lastname'])) {
                            // XC does not support single word customer name
                            $tmp['lastname'] = $tmp['firstname'];
                        }

                        $this->updateAddress($tmp);

                        $profile = $this->getCartProfile();

                        // email, name
                        if ($buyer && !$profile->getLogin()) {
                            $uinfo = array();
                            $uinfo['email'] = $buyer['Email'][0]['#'];
                            // list($uinfo['firstname'], $uinfo['lastname']) = explode(' ', $buyer['Name'][0]['#'], 2);

                            // update email
                            $profile = $this->getCartProfile();
                            $profile->setLogin($uinfo['email']);
                            $this->getCart()->setProfile($profile);
                        }
                    }
                }

                $orderids = $this->placeAmazonOrder($payment_method_text);

                AMZ::func_amazon_pa_save_order_extra($orderids, 'AmazonOrderReferenceId', $amazon_pa_orefid);

                $order_status = \XLite\Model\Order\Status\Payment::STATUS_CANCELED;
                $amz_authorized = false;
                $amz_authorization_id = '';
                $amz_captured = false;
                $amz_capture_id = '';
                $advinfo = array();

                // Authorize
                $_tmp = array(
                    'AmazonOrderReferenceId' => $amazon_pa_orefid,
                    'AuthorizationAmount.Amount' => $cart_total_cost,
                    'AuthorizationAmount.CurrencyCode' => \XLite\Core\Config::getInstance()->Amazon->PayWithAmazon->amazon_pa_currency,
                    'AuthorizationReferenceId' => 'auth_' . \XLite\Core\Config::getInstance()->Amazon->PayWithAmazon->amazon_pa_order_prefix . '_' . $orderids,
                    'SellerAuthorizationNote' => '',
                );
                if (\XLite\Core\Config::getInstance()->Amazon->PayWithAmazon->amazon_pa_capture_mode == 'C') {
                    // capture immediate
                    $_tmp['CaptureNow'] = 'true';
                }
                if (\XLite\Core\Config::getInstance()->Amazon->PayWithAmazon->amazon_pa_mode == 'test' && !empty($customer_notes)) {
                    // simulate decline
                    if ($customer_notes == 'decline') {
                        $_tmp['SellerAuthorizationNote'] = urlencode('{"SandboxSimulation":{"State":"Declined","ReasonCode":"AmazonRejected"}}');
                    }
                }
                if (\XLite\Core\Config::getInstance()->Amazon->PayWithAmazon->amazon_pa_sync_mode == 'S') {
                    // sync request (returns only "open" or "declined" status, no "pending")
                    $_tmp['TransactionTimeout'] = '0';
                }
                $res = AMZ::func_amazon_pa_request('Authorize', $_tmp);
                if ($res) {
                    $_auth_details = AMZ::func_array_path($res, 'AuthorizeResponse/AuthorizeResult/AuthorizationDetails/0/#');
                    if ($_auth_details) {
                        $amz_authorization_id = $_auth_details['AmazonAuthorizationId'][0]['#'];
                        $_reply_status = $_auth_details['AuthorizationStatus'][0]['#']['State'][0]['#'];
                        $advinfo[] = "AmazonAuthorizationId: $amz_authorization_id";
                        $advinfo[] = "AuthorizationStatus: $_reply_status";
                        AMZ::func_amazon_pa_save_order_extra($orderids, 'amazon_pa_auth_id', $amz_authorization_id);
                        AMZ::func_amazon_pa_save_order_extra($orderids, 'amazon_pa_auth_status', $_reply_status);

                        if ($_reply_status == 'Declined') {
                            $order_status = \XLite\Model\Order\Status\Payment::STATUS_DECLINED;
                        }

                        if ($_reply_status == 'Pending') {
                            $order_status = \XLite\Model\Order\Status\Payment::STATUS_QUEUED; // wait for IPN message
                        }

                        if ($_reply_status == 'Open') {
                            $amz_authorized = true;
                        }

                        if ($_reply_status == 'Closed') {
                            // capture now mode
                            if (\XLite\Core\Config::getInstance()->Amazon->PayWithAmazon->amazon_pa_capture_mode == 'C') {
                                $amz_authorized = true;
                                $amz_captured = true;
                                $_capt_id = $_auth_details['IdList'][0]['#']['member'][0]['#'];
                                AMZ::func_amazon_pa_save_order_extra($orderids, 'amazon_pa_capture_id', $_capt_id);
                            }
                        }

                    } else {
                        // log error
                        AMZ::func_amazon_pa_error('Unexpected authorize reply: res=' . print_r($res, true));
                    }
                }

                if ($amz_authorized) {
                    if ($amz_captured) {
                        // capture now mode, order is actually processed here
                        $order_status = \XLite\Model\Order\Status\Payment::STATUS_PAID;
                    } else {
                        // pre-auth
                        $order_status = \XLite\Model\Order\Status\Payment::STATUS_AUTHORIZED;
                    }
                }

                // change order status
                AMZ::func_change_order_status($orderids, $order_status, join("\n", $advinfo));

                if ($amz_authorized) {
                    $this->getCart()->processSucceed();
                    \XLite\Core\Database::getEM()->flush();
                }

                // show invoice or error message
                $this->orderRedirect($orderids, $order_status);

                break;

            } // switch mode

        } // post

        parent::handleRequest();
    }

    /**
     * Place order and return order ID
     *
     * @return integer
     */
    protected function placeAmazonOrder($payment_method_text)
    {
        $cart = $this->getCart();

        if (isset(\XLite\Core\Request::getInstance()->notes)) {
            $cart->setNotes(\XLite\Core\Request::getInstance()->notes);
        }

        $cart->setDate(\XLite\Core\Converter::time());
        $cart->assignOrderNumber();

        $cart->setPaymentStatus(\XLite\Model\Order\Status\Payment::STATUS_QUEUED);
        $cart->setShippingStatus(\XLite\Model\Order\Status\Shipping::STATUS_NEW);
        
        // apply $payment_method_text payment method
        $_tmp_method = \XLite\Core\Database::getRepo('XLite\Model\Payment\Method')->findBy(array('service_name' => 'PayWithAmazon'));
        if ($_tmp_method) {
            $_tmp_method = $_tmp_method[0];
        } else {
            // auto create it
            $_tmp_method = new \XLite\Model\Payment\Method();
            $_tmp_method->setClass('Model\Payment\Processor\Offline');
            $_tmp_method->setServiceName('PayWithAmazon');
            $_tmp_method->setName($payment_method_text);
            $_tmp_method->setModuleName('Amazon_PayWithAmazon');
        }
        $this->getCart()->setPaymentMethod($_tmp_method);

        $this->processCartProfile();

        // Mark all addresses as non-work
        if ($cart->getOrigProfile()) {
            foreach ($cart->getOrigProfile()->getAddresses() as $address) {
                $address->setIsWork(false);
            }
        }

        if ($cart->getProfile()) {
            foreach ($cart->getProfile()->getAddresses() as $address) {
                $address->setIsWork(false);
            }
        }


        // $this->updateCart(); // old way produce fingerprint warning in logs
        $this->getCart()->updateOrder();
        \XLite\Core\Database::getRepo('XLite\Model\Cart')->update($this->getCart());

        // Register 'Place order' event in the order history
        \XLite\Core\OrderHistory::getInstance()->registerPlaceOrder($this->getCart()->getOrderId());

        \XLite\Core\Database::getEM()->flush();

        return $this->getCart()->getOrderId();
    }

    /**
     * Process cart profile
     *
     * @return boolean
     */
    protected function processCartProfile()
    {
        $isAnonymous = $this->isAnonymous();

        $cart = $this->getCart();
        if ($isAnonymous) {
            // Merge
            $this->mergeAnonymousProfile($cart);

        } else {
            // Clone profile
            $this->cloneProfile($cart);
        }
    }

    /**
     * Return true if current profile is anonymous
     *
     * @return boolean
     */
    public function isAnonymous()
    {
        $cart = $this->getCart();

        return !$cart->getProfile() || $cart->getProfile()->getAnonymous();
    }

    /**
     * Merge anonymous profile
     *
     * @return void
     */
    protected function mergeAnonymousProfile($cart)
    {
        $profile = \XLite\Core\Database::getRepo('XLite\Model\Profile')
            ->findOneAnonymousByProfile($cart->getProfile());

        if ($profile) {
            $profile->mergeWithProfile(
                $cart->getProfile(),
                \XLite\Model\Profile::MERGE_ALL ^ \XLite\Model\Profile::MERGE_ORDERS
            );

        } else {
            $profile = $cart->getProfile()->cloneEntity();
            $profile->setOrder(null);
            $profile->setAnonymous(true);
        }
        $cart->setOrigProfile($profile);

        \XLite\Core\Database::getEM()->flush();
    }

    /**
     * Clone profile and move profile to original profile
     *
     * @return void
     */
    protected function cloneProfile($cart)
    {
        $origProfile = $cart->getProfile();
        $profile = $origProfile->cloneEntity();

        // Assign cloned order's profile
        $cart->setProfile($profile);
        $profile->setOrder($cart);

        // Save old profile as original profile
        $cart->setOrigProfile($origProfile);
        $origProfile->setOrder(null);

        \XLite\Core\Database::getEM()->flush();
    }

    /**
     * Redirect customer to invoice or order failed page
     *
     * @param integer $orderids     Order ID
     * @param string  $order_status Order payment status
     *
     * @return void
     */
    protected function orderRedirect($orderids, $order_status) 
    {
        if ($order_status == \XLite\Model\Order\Status\Payment::STATUS_CANCELED || $order_status == \XLite\Model\Order\Status\Payment::STATUS_DECLINED) {

            $reason = '';
            if ($order_status == \XLite\Model\Order\Status\Payment::STATUS_CANCELED) {
                // some error
                $reason = 'Some error occurred during transaction, please try again later or use another payment method.';
            } elseif ($order_status == \XLite\Model\Order\Status\Payment::STATUS_DECLINED) {
                // transaction declined
                $reason = 'Transaction is declined';
            }

            // error message
            \XLite\Core\TopMessage::addError($reason);

            $this->setReturnURL($this->buildURL('checkoutFailed'));
            $this->redirect();

        } else {

            // show invoice
            \XLite\Core\Session::getInstance()->last_order_id = $orderids;
            $cart = $this->getCart();

            $this->setReturnURL(
                $this->buildURL(
                    'checkoutSuccess',
                    '',
                    $cart->getOrderNumber()
                        ? array('order_number' => $cart->getOrderNumber())
                        : array('order_id' => $cart->getOrderId())
                )
            );
            $this->redirect();
        }
    }

    /**
     * Update address
     *
     * @return void
     */
    protected function updateAddress($data)
    {
        if (!is_array($data)) {
            return;
        }

        $profile = $this->getCartProfile();

        if (!$profile || !$profile->getProfileId()) {
            return;
        }

        // Initialize address received from Amazon
        $amazonAddress = new \XLite\Model\Address;
        $amazonAddress->map($this->prepareAddressData($data));
        $amazonAddress->setIsShipping(true);
        $amazonAddress->setIsBilling(true);

        $sameAddress = null;

        $doFlush = false;

        foreach ($profile->getAddresses() as $addr) {

            $isEqual = $addr->isEqualAddress($amazonAddress);
            $status = false;

            if ($isEqual && !$sameAddress) {
                // The same address found in address book - mark this as billing/shipping
                $sameAddress = $addr;
                $status = true;
            }

            if ($addr->getIsWork() && !$isEqual) {
                // Temporary address is different from amazon address - remove this
                $profile->getAddresses()->removeElement($addr);
                \XLite\Core\Database::getEM()->remove($addr);
                $doFlush = true;

            } else {
                // Change shipping/billing status of address
                if ($addr->getIsShipping() != $status) {
                    $addr->setIsShipping($status);
                    $doFlush = true;
                }
                if ($addr->getIsBilling() != $status) {
                    $addr->setIsBilling($status);
                    $doFlush = true;
                }
            }
        }

        if (!$sameAddress) {
            // Amazon address not found in address book - add this
            $amazonAddress->setProfile($profile);
            $amazonAddress->setIsWork(true);
            $profile->addAddresses($amazonAddress);
            \XLite\Core\Database::getEM()->persist($amazonAddress);
            $doFlush = true;
        }

        if ($doFlush) {
            \XLite\Core\Database::getEM()->flush();
        }

        \XLite\Core\Session::getInstance()->same_address = true;
    }

    /**
     * Prepare data to map into address object
     *
     * @param array $data Input data
     *
     * @return array
     */
    protected function prepareAddressData(array $data)
    {
        unset($data['save_in_book']);

        $requiredFields = \XLite\Core\Database::getRepo('XLite\Model\AddressField')->getShippingRequiredFields();

        foreach ($requiredFields as $fieldName) {
            if (!isset($data[$fieldName]) && \XLite\Model\Address::getDefaultFieldValue($fieldName)) {
                $data[$fieldName] = \XLite\Model\Address::getDefaultFieldValue($fieldName);
            }
        }

        return $data;
    }

    /**
     * Get 'Terms and conditions' page URL
     *
     * @return string
     */
    public function getTermsURL()
    {
        return \XLite\Core\Config::getInstance()->General->terms_url;
    }

    /**
     * Check - controller must work in secure zone or not
     *
     * @return boolean
     */
    public function isSecure()
    {
        return \XLite\Core\Config::getInstance()->Security->customer_security;
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

    /**
     * Handle IPN callback from Amazon
     *
     * @return void
     */
    protected function handleIPNCallback() {

        $request_body = file_get_contents('php://input');
        if (empty($request_body)) {
            // empty request
            return;
        }

        $message = json_decode($request_body, true);
        $json_error = json_last_error();
        if ($json_error != 0) {
            AMZ::func_amazon_pa_error("incorrect IPN call (can not parse json data (err=$json_error) request=" . $request_body);
            return;
        }

        // verify signature
        if (!AMZ::func_amazon_pa_ipn_verify_singature($message)) {
            AMZ::func_amazon_pa_error("ERROR: can't verify signature. IPN message=" . print_r($message, true));
            return;
        }

        // handle message
        AMZ::func_amazon_pa_debug("IPN message received: $message[Message]");
        $notification = json_decode($message['Message'], true);
        $res = AMZ::func_xml_parse($notification['NotificationData'], $parse_error);
        $advinfo = array();

        switch ($notification['NotificationType']) {

            case 'PaymentAuthorize':
                $_auth_details = AMZ::func_array_path($res, 'AuthorizationNotification/AuthorizationDetails/0/#');
                if ($_auth_details) {
                    $_reply_status = $_auth_details['AuthorizationStatus'][0]['#']['State'][0]['#'];
                    $_reply_reason = $_auth_details['AuthorizationStatus'][0]['#']['ReasonCode'][0]['#'];
                    $_authorization_id = $_auth_details['AmazonAuthorizationId'][0]['#'];
                    $_oid = str_replace('auth_', '', $_auth_details['AuthorizationReferenceId'][0]['#']);

                    $advinfo[] = "AmazonAuthorizationId: $_authorization_id";
                    $advinfo[] = "AuthorizationStatus: $_reply_status";
                    AMZ::func_amazon_pa_save_order_extra($_oid, 'amazon_pa_auth_status', $_reply_status);
                    if (!empty($_reply_reason)) {
                        $advinfo[] = "AuthorizationReason: $_reply_reason";
                    }

                    if ($_reply_status == 'Open') {
                        if (\XLite\Core\Config::getInstance()->Amazon->PayWithAmazon->amazon_pa_capture_mode == 'A') {
                            // authorized
                            AMZ::func_change_order_status($_oid, \XLite\Model\Order\Status\Payment::STATUS_AUTHORIZED, join("\n", $advinfo));
                        }
                    }
                    if ($_reply_status == 'Declined') {
                        // declined
                        AMZ::func_change_order_status($_oid, \XLite\Model\Order\Status\Payment::STATUS_DECLINED, join("\n", $advinfo));
                    }
                }
                break;

            case 'PaymentCapture':
                $_capt_details = AMZ::func_array_path($res, 'CaptureNotification/CaptureDetails/0/#');
                if ($_capt_details) {
                    $_reply_status = $_capt_details['CaptureStatus'][0]['#']['State'][0]['#'];
                    $_reply_reason = $_capt_details['CaptureStatus'][0]['#']['ReasonCode'][0]['#'];
                    $_capture_id = $_capt_details['AmazonCaptureId'][0]['#'];

                    $_oid = str_replace('capture_', '', $_capt_details['CaptureReferenceId'][0]['#']);
                    $_oid = str_replace('auth_', '', $_oid); // captureNow mode

                    $advinfo[] = "AmazonCaptureId: $_capture_id";
                    $advinfo[] = "CaptureStatus: $_reply_status";
                    if (!empty($_reply_reason)) {
                        $advinfo[] = "CaptureReason: $_reply_reason";
                    }
                    AMZ::func_amazon_pa_save_order_extra($_oid, 'amazon_pa_capture_status', $_reply_status);
                    AMZ::func_amazon_pa_save_order_extra($_oid, 'amazon_pa_capture_id', $_capture_id); // captureNow mode

                    if ($_reply_status == 'Completed') {
                        // captured, order is processed
                        AMZ::func_change_order_status($_oid, \XLite\Model\Order\Status\Payment::STATUS_PAID, join("\n", $advinfo));
                    }
                    if ($_reply_status == 'Declined') {
                        // declined
                        AMZ::func_change_order_status($_oid, \XLite\Model\Order\Status\Payment::STATUS_DECLINED, join("\n", $advinfo));
                    }
                }
                break;

            case 'PaymentRefund':
                $_ref_details = AMZ::func_array_path($res, 'RefundNotification/RefundDetails/0/#');
                if ($_ref_details) {
                    $amz_ref_id = $_ref_details['AmazonRefundId'][0]['#'];
                    $_reply_status = $_ref_details['RefundStatus'][0]['#']['State'][0]['#'];
                    $_reply_reason = $_ref_details['RefundStatus'][0]['#']['ReasonCode'][0]['#'];
                    $_oid = str_replace('refund_', '', $_ref_details['RefundReferenceId'][0]['#']);

                    $advinfo[] = "AmazonRefundId: $amz_ref_id";
                    $advinfo[] = "RefundStatus: $_reply_status";
                    if (!empty($_reply_reason)) {
                        $advinfo[] = "RefundReason: $_reply_reason";
                    }
                    AMZ::func_amazon_pa_save_order_extra($orderid, 'amazon_pa_refund_status', $_reply_status);

                    if ($_reply_status == 'Completed') {
                        // refunded
                        AMZ::func_change_order_status($_oid, \XLite\Model\Order\Status\Payment::STATUS_REFUNDED, join("\n", $advinfo));
                    }
                }
                break;
        }
    }

    // workaround for compatibility with XP (XP's checkout/script.twig produce error without it)
    public function getXpcPaymentIds() { return array(); }
    public function isCheckoutReady()  { return false; }
    public function checkCheckoutAction()  { return false; }
    public function getSaveCardBoxClass() { return ''; }
    public function showSaveCardBox() { return false; }
}
