<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Checkout;

/**
 * Shipping methods list
 *
 * @ListChild (list="checkout.shipping.selected.sub.payment", weight="300")
 */
class PaymentMethodsList extends \XLite\View\AView
{

    /**
     * Payed cart flag
     *
     * @var   boolean
     */
    protected $isPayedCart;

    /**
     * Get JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = 'checkout/steps/shipping/parts/paymentMethods.js';

        return $list;
    }

    /**
     * Return flag if the cart has been already payed
     *
     * @return boolean
     */
    protected function isPayedCart()
    {
        if (!isset($this->isPayedCart)) {

            $this->isPayedCart = $this->getCart()->isPayed();
        }

        return $this->isPayedCart;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'checkout/steps/shipping/parts/paymentMethods.twig';
    }

    /**
     * Prepare payment method icon
     *
     * @param string $icon Icon local path
     *
     * @return string
     */
    protected function preparePaymentMethodIcon($icon)
    {
        return \XLite\Core\Layout::getInstance()->getResourceWebPath($icon, \XLite\Core\Layout::WEB_PATH_OUTPUT_URL);
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible();
    }

    /**
     * Return list of available payment methods
     *
     * @return array
     */
    protected function getPaymentMethods()
    {
        return $this->getCart()->getPaymentMethods();
    }

    /**
     * Check - payment method is selected or not
     *
     * @param \XLite\Model\Payment\Method $method Payment methods
     *
     * @return boolean
     */
    protected function isPaymentSelected(\XLite\Model\Payment\Method $method)
    {
        $currentMethodServiceName = $this->getCart()->getPaymentMethod()
            ? $this->getCart()->getPaymentMethod()->getServiceName()
            : null;

        $methodServiceName = $method
            ? $method->getServiceName()
            : null;

        return $currentMethodServiceName
            && $methodServiceName
            && $currentMethodServiceName === $methodServiceName;
    }
}
