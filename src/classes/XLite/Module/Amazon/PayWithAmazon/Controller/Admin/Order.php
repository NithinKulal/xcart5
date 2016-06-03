<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\Amazon\PayWithAmazon\Controller\Admin;
use XLite\Module\Amazon\PayWithAmazon\AMZ as AMZ;

class Order extends \XLite\Controller\Admin\Order implements \XLite\Base\IDecorator
{
    public function handleRequest() {

        if (!\XLite\Core\Request::getInstance()->isPost()) {
            // get request
            return parent::handleRequest();
        }

        $orderid = \XLite\Core\Request::getInstance()->order_id;

        $order = \XLite\Core\Database::getRepo('XLite\Model\Order')->find($orderid);
        if ($order) {
            // \XLite\Model\Cart::setObject($order); // need this? Who knows
        } else {
            return parent::handleRequest();
        }

        $extra = array();
        $_details = $order->getDetails();
        if ($_details) {
            foreach ($_details as $_detail) {
                $extra[$_detail->getName()] = $_detail->getValue();
            }
        }

        $errMsg = '';
        $orderStatus = '';
        $advinfo = array();

        // post
        switch (\XLite\Core\Request::getInstance()->action) {
        case 'amz_capture':

            $amz_captured = false;

            $res = AMZ::func_amazon_pa_request('Capture', array(
                'AmazonAuthorizationId' => $extra['amazon_pa_auth_id'],
                'CaptureAmount.Amount' => $order->getTotal(),
                'CaptureAmount.CurrencyCode' => \XLite\Core\Config::getInstance()->Amazon->PayWithAmazon->amazon_pa_currency,
                'CaptureReferenceId' => 'capture_' . $orderid,
                'SellerCaptureNote' => '',
            ));
            if ($res) {
                $_capt_details = AMZ::func_array_path($res, 'CaptureResponse/CaptureResult/CaptureDetails/0/#');
                if ($_capt_details) {
                    $amz_capture_id = $_capt_details['AmazonCaptureId'][0]['#'];
                    $_reply_status = $_capt_details['CaptureStatus'][0]['#']['State'][0]['#'];
                    $amz_captured = ($_reply_status == 'Completed');
                    $captured_total = $_capt_details['CaptureAmount'][0]['#']['Amount'][0]['#'];

                    $advinfo[] = "AmazonCaptureId: $amz_capture_id";
                    $advinfo[] = "CaptureStatus: $_reply_status";
                    AMZ::func_amazon_pa_save_order_extra($orderid, 'amazon_pa_capture_id', $amz_capture_id);
                    AMZ::func_amazon_pa_save_order_extra($orderid, 'amazon_pa_capture_status', $_reply_status);

                    if ($_reply_status == 'Declined') {
                        $orderStatus = \XLite\Model\Order\Status\Payment::STATUS_DECLINED;
                    }
                    $errMsg = "Status=$_reply_status";
                } else {
                    // log error
                    $errMsg = 'Unexpected Capture reply';
                    AMZ::func_amazon_pa_error('Unexpected Capture reply: ' . AMZ::func_xml_format($res));
                }
            }

            if ($amz_captured) {
                // captured
                $orderStatus = \XLite\Model\Order\Status\Payment::STATUS_PAID;
            }

            if (!empty($orderStatus)) {
                AMZ::func_change_order_status($orderid, $orderStatus, join("\n", $advinfo));
            }

            if ($amz_captured) {
                \XLite\Core\TopMessage::addInfo('Payment was captured successfully.');
            } else {
                \XLite\Core\TopMessage::addError('Payment capture error: ' . $errMsg);
            }
            break;

        case 'amz_void':
            $amz_voided = false;

            $res = AMZ::func_amazon_pa_request('CloseAuthorization', array(
                'AmazonAuthorizationId' => $extra['amazon_pa_auth_id'],
                'ClosureReason' => '',
            ));

            if ($res) {
                $amz_voided = true;
            } else {
                $errMsg = 'Void error';
            }

            if ($amz_voided) {
                AMZ::func_change_order_status($orderid, \XLite\Model\Order\Status\Payment::STATUS_CANCELED);

                \XLite\Core\TopMessage::addInfo('Payment voided successfully');
            } else {
                \XLite\Core\TopMessage::addError('Void error:' . $errMsg);
            }
            break;

        case 'amz_refund':
            $amz_refunded = false;

            $res = AMZ::func_amazon_pa_request('Refund', array(
                'AmazonCaptureId' => $extra['amazon_pa_capture_id'],
                'RefundAmount.Amount' => $order->getTotal(),
                'RefundAmount.CurrencyCode' => \XLite\Core\Config::getInstance()->Amazon->PayWithAmazon->amazon_pa_currency,
                'RefundReferenceId' => 'refund_' . $orderid,
                'SellerRefundNote' => '',
            ));
            if ($res) {
                $_ref_details = AMZ::func_array_path($res, 'RefundResponse/RefundResult/RefundDetails/0/#');
                if ($_ref_details) {
                    $amz_ref_id = $_ref_details['AmazonRefundId'][0]['#'];
                    $_reply_status = $_ref_details['RefundStatus'][0]['#']['State'][0]['#'];
                    $amz_refunded = ($_reply_status == 'Completed');
                    $refunded_total = $_ref_details['RefundAmount'][0]['#']['Amount'][0]['#'];

                    $advinfo[] = "AmazonRefundId: $amz_ref_id";
                    $advinfo[] = "RefundStatus: $_reply_status";
                    AMZ::func_amazon_pa_save_order_extra($orderid, 'amazon_pa_refund_id', $amz_ref_id);
                    AMZ::func_amazon_pa_save_order_extra($orderid, 'amazon_pa_refund_status', $_reply_status);

                    $errMsg = "Status=$_reply_status";
                } else {
                    // log error
                    $errMsg = 'Unexpected Refund reply';
                    AMZ::func_amazon_pa_error('Unexpected Refund reply: ' . AMZ::func_xml_format($res));
                }
            }

            if ($amz_refunded) {
                // status refunded
                AMZ::func_change_order_status($orderid, \XLite\Model\Order\Status\Payment::STATUS_REFUNDED, join("\n", $advinfo));

                \XLite\Core\TopMessage::addInfo('Payment refunded successfully');
            } else {
                if ($_reply_status == 'Pending') {
                    \XLite\Core\TopMessage::addInfo('Refund in progress...');
                } else {
                    \XLite\Core\TopMessage::addError('Refund error:' . $errMsg);
                }
            }

            break;

        case 'amz_refresh':

            $res = AMZ::func_amazon_pa_request('GetAuthorizationDetails', array(
                'AmazonAuthorizationId' => $extra['amazon_pa_auth_id'],
            ));
            $_auth_details = AMZ::func_array_path($res, 'GetAuthorizationDetailsResponse/GetAuthorizationDetailsResult/AuthorizationDetails/0/#');
            if ($_auth_details) {
                $_reply_status = $_auth_details['AuthorizationStatus'][0]['#']['State'][0]['#'];
                $_reply_reason = $_auth_details['AuthorizationStatus'][0]['#']['ReasonCode'][0]['#'];
                $_oid = str_replace('auth_', '', $_auth_details['AuthorizationReferenceId'][0]['#']);

                AMZ::func_amazon_pa_save_order_extra($_oid, 'amazon_pa_auth_status', $_reply_status);
                $advinfo[] = "AuthorizationStatus: $_reply_status";
                if (!empty($_reply_reason)) {
                    $advinfo[] = "AuthorizationReason: $_reply_reason";
                }

                if ($_reply_status == 'Open') {
                    if (\XLite\Core\Config::getInstance()->Amazon->PayWithAmazon->amazon_pa_capture_mode == 'A') {
                        // pre-authorized
                        AMZ::func_change_order_status($_oid, \XLite\Model\Order\Status\Payment::STATUS_AUTHORIZED, join("\n", $advinfo));
                    }
                }

                if ($_reply_status == 'Closed') {
                    $_a_amnt = $_auth_details['AuthorizationAmount'][0]['#']['Amount'][0]['#'];
                    $_c_amnt = $_auth_details['CapturedAmount'][0]['#']['Amount'][0]['#'];
                    if ($_c_amnt > 0 && $_c_amnt == $_a_amnt) {

                        // capture now mode, funds were captured successfully, save captureID
                        $_capt_id = $_auth_details['IdList'][0]['#']['member'][0]['#'];
                        AMZ::func_amazon_pa_save_order_extra($_oid, 'amazon_pa_capture_id', $_capt_id);
                        $advinfo[] = "AmazonCaptureId: $_capt_id";

                        AMZ::func_change_order_status($_oid, \XLite\Model\Order\Status\Payment::STATUS_PAID, join("\n", $advinfo));
                    }
                }

                if ($_reply_status == 'Declined') {
                    // declined
                    AMZ::func_change_order_status($_oid, \XLite\Model\Order\Status\Payment::STATUS_DECLINED, join("\n", $advinfo));
                }
            }
            break;

        case 'amz_refresh_refund_status':

            $res = AMZ::func_amazon_pa_request('GetRefundDetails', array(
                'AmazonRefundId' => $extra['amazon_pa_refund_id'],
            ));
            $_ref_details = AMZ::func_array_path($res, 'GetRefundDetailsResponse/GetRefundDetailsResult/RefundDetails/0/#');
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
                AMZ::func_amazon_pa_save_order_extra($_oid, 'amazon_pa_refund_status', $_reply_status);

                if ($_reply_status == 'Completed') {
                    // refunded
                    \XLite\Core\TopMessage::addInfo('Payment refunded successfully');
                    AMZ::func_change_order_status($_oid, \XLite\Model\Order\Status\Payment::STATUS_REFUNDED, join("\n", $advinfo));
                }
            }

            break;

        case 'amz_refresh_capture_status':
            // not used
            break;

        } // switch

        return parent::handleRequest();
    }

}

?>
