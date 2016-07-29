<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\Amazon\PayWithAmazon\View;

abstract class AView extends \XLite\View\AView implements \XLite\Base\IDecorator
{

    public function getMetaTags()
    {
        $list = parent::getMetaTags();

        $amazonSid = \XLite\Core\Config::getInstance()->Amazon->PayWithAmazon->amazon_pa_sid;

        if (!empty($amazonSid)) {
            $mode = \XLite\Core\Config::getInstance()->Amazon->PayWithAmazon->amazon_pa_mode;
            $ism = method_exists('\XLite\Core\Request', 'isMobileDevice') && \XLite\Core\Request::isMobileDevice() ? 'true' : 'false';
            $list[] = "<script type=\"text/javascript\">var AMAZON_PA_CONST = {SID:'$amazonSid',MODE:'$mode',MOBILE:$ism};</script>";
        }

        return $list;
    }

    // looks like there is no way to display "{" and "}" in flexy, so use this hack
    public function getLdelim() {
        return '{';
    }

    public function getRdelim() {
        return '}';
    }

    public function isRefundButtonVisible() {
        $order = $this->get('order');
        if ($order) {
            if ($order->getPaymentStatusCode() == \XLite\Model\Order\Status\Payment::STATUS_PAID) {
                return true;
            }
        }
        return false;
    }

    public function isRefreshButtonVisible() {
        $order = $this->get('order');
        if ($order) {
            if ($order->getPaymentStatusCode() == \XLite\Model\Order\Status\Payment::STATUS_QUEUED) {
                return true;
            }
        }
        return false;
    }

    public function isCaptureButtonVisible() {
        $order = $this->get('order');
        if ($order) {
            if ($order->getPaymentStatusCode() == \XLite\Model\Order\Status\Payment::STATUS_AUTHORIZED) {
                return true;
            }
        }
        return false;
    }

    public function getOrderDetail($str) {
        $order = $this->get('order');
        if ($order && $order->getDetail($str)) {
            return (string)$order->getDetail($str)->getValue();
        } else {
            return '';
        }
    }

    public function isAmazonControlsVisible() {
        $order = $this->get('order');
        return (
            $this->getOrderDetail('AmazonOrderReferenceId')
            && in_array($order->getPaymentStatusCode(), array(
                \XLite\Model\Order\Status\Payment::STATUS_AUTHORIZED, 
                \XLite\Model\Order\Status\Payment::STATUS_PAID,
                \XLite\Model\Order\Status\Payment::STATUS_QUEUED,
            ))
        );
    }

    protected function getThemeFiles($adminZone = null)
    {
        $list = parent::getThemeFiles($adminZone);

        $list[static::RESOURCE_JS][] = 'modules/Amazon/PayWithAmazon/func.js';

        return $list;
    }

    public function getAmazonJSURL()
    {
        $url = '';
        $amazonSid = \XLite\Core\Config::getInstance()->Amazon->PayWithAmazon->amazon_pa_sid;
        $amazonCurr = \XLite\Core\Config::getInstance()->Amazon->PayWithAmazon->amazon_pa_currency;

        if (!empty($amazonSid)) {

            if (\XLite\Core\Config::getInstance()->Amazon->PayWithAmazon->amazon_pa_mode == 'live') {
                switch ($amazonCurr) {
                    case 'EUR':
                        $url = 'https://static-eu.payments-amazon.com/OffAmazonPayments/de/js/Widgets.js';
                        break;
                    case 'GBP':
                        $url = 'https://static-eu.payments-amazon.com/OffAmazonPayments/uk/js/Widgets.js';
                        break;
                    default: // USD
                        $url = 'https://static-na.payments-amazon.com/OffAmazonPayments/us/js/Widgets.js';
                        break;
                }
            } else {
                switch ($amazonCurr) {
                    case 'EUR':
                        $url = 'https://static-eu.payments-amazon.com/OffAmazonPayments/de/sandbox/js/Widgets.js';
                        break;
                    case 'GBP':
                        $url = 'https://static-eu.payments-amazon.com/OffAmazonPayments/uk/sandbox/js/Widgets.js';
                        break;
                    default: // USD
                        $url = 'https://static-na.payments-amazon.com/OffAmazonPayments/us/sandbox/js/Widgets.js';
                        break;
                }
            }
            $url .= '?sellerId=' . $amazonSid;
        }

        return $url;
    }

    public function getAmazonButtonURL()
    {
        $url = '';
        $amazonSid = \XLite\Core\Config::getInstance()->Amazon->PayWithAmazon->amazon_pa_sid;
        $amazonCurr = \XLite\Core\Config::getInstance()->Amazon->PayWithAmazon->amazon_pa_currency;

        if (!empty($amazonSid)) {

            if (\XLite\Core\Config::getInstance()->Amazon->PayWithAmazon->amazon_pa_mode == 'live') {
                switch ($amazonCurr) {
                    case 'EUR':
                        $url = 'https://payments.amazon.de/gp/widgets/button';
                        break;
                    case 'GBP':
                        $url = 'https://payments.amazon.co.uk/gp/widgets/button';
                        break;
                    default: // USD
                        $url = 'https://payments.amazon.com/gp/widgets/button';
                        break;
                }
            } else {
                switch ($amazonCurr) {
                    case 'EUR':
                        $url = 'https://payments-sandbox.amazon.de/gp/widgets/button';
                        break;
                    case 'GBP':
                        $url = 'https://payments-sandbox.amazon.co.uk/gp/widgets/button';
                        break;
                    default: // USD
                        $url = 'https://payments-sandbox.amazon.com/gp/widgets/button';
                        break;
                }
            }
            $url .= '?sellerId=' . $amazonSid . '&size=large&color=orange';
        }

        return $url;
    }

    public function isPayWithAmazonActive()
    {
        // disable if no seller id is specified
        if (empty(\XLite\Core\Config::getInstance()->Amazon->PayWithAmazon->amazon_pa_sid)) {
            return false;
        }

        // TMP: disable if Mobile skin is used
        if ($this->isMobileSkinUsed()) {
            return false;
        }

        if ($this->getCart() && !$this->getCart()->checkCart()) {
            return false;
        }

        return true;
    }

    public function isMobileSkinUsed()
    {
        // mobile condition: method_exists('\XLite\Core\Request', 'isMobileDevice') && \XLite\Core\Request::isMobileDevice()
        // TODO: check switch to desktop version button
        $module = \XLite\Core\Database::getRepo('XLite\Model\Module')->findOneBy(array('author' => 'XC', 'name' => 'Mobile'));
        if ($module && $module->getEnabled() && !\XLite::isAdminZone() && \XLite\Core\Request::isMobileDevice()) {
            return true;
        } else {
            return false;
        }
    }

}

