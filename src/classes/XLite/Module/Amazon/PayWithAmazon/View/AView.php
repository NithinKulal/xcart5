<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\Amazon\PayWithAmazon\View;

abstract class AView extends \XLite\View\AView implements \XLite\Base\IDecorator
{
    public function isRefundButtonVisible() {
        $order = $this->get('order');
        if ($order) {
            if ($order->getPaymentStatusCode() === \XLite\Model\Order\Status\Payment::STATUS_PAID
                || ($order->getPaymentStatusCode() === \XLite\Model\Order\Status\Payment::STATUS_REFUNDED
                    && $order->getDetail('amazon_pa_refund_status')
                    && $order->getDetail('amazon_pa_refund_status')->getValue() === 'Pending'
                )
            ) {
                return true;
            }
        }
        return false;
    }

    public function isRefreshButtonVisible() {
        /** @var \XLite\Model\Order $order */
        $order = $this->get('order');
        if ($order) {
            if ($order->getPaymentStatusCode() === \XLite\Model\Order\Status\Payment::STATUS_QUEUED) {
                return true;
            }
        }
        return false;
    }

    public function isCaptureButtonVisible() {
        $order = $this->get('order');
        if ($order) {
            if ($order->getPaymentStatusCode() === \XLite\Model\Order\Status\Payment::STATUS_AUTHORIZED) {
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

    public function isAmazonControlsVisible()
    {
        $order = $this->get('order');
        return (
            $this->getOrderDetail('AmazonOrderReferenceId')
            && (in_array($order->getPaymentStatusCode(), array(
                \XLite\Model\Order\Status\Payment::STATUS_AUTHORIZED,
                \XLite\Model\Order\Status\Payment::STATUS_PAID,
                \XLite\Model\Order\Status\Payment::STATUS_QUEUED,
            ))
                || ($order->getPaymentStatusCode() === \XLite\Model\Order\Status\Payment::STATUS_REFUNDED
                    && $order->getDetail('amazon_pa_refund_status')
                    && $order->getDetail('amazon_pa_refund_status')->getValue() === 'Pending'
                )
        ));
    }

    /**
     * @param boolean|null $adminZone
     *
     * @return array
     */
    protected function getThemeFiles($adminZone = null)
    {
        $list = parent::getThemeFiles($adminZone);
        $api = \XLite\Module\Amazon\PayWithAmazon\Main::getApi();

        if ($api->isConfigured()) {
            $list[static::RESOURCE_JS][] = [
                'url' => $api->getJsUrl(), // todo: allow async attribute for script tag
            ];
            $list[static::RESOURCE_JS][] = 'modules/Amazon/PayWithAmazon/func.js';

            $list[static::RESOURCE_CSS][] = 'modules/Amazon/PayWithAmazon/checkout_button/style.css';
        }

        return $list;
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
