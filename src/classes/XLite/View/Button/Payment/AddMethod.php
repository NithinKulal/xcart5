<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Button\Payment;

/**
 * Add payment method popup button
 */
class AddMethod extends \XLite\View\Button\APopupButton
{
    /**
     * Name of "payment methods type" parameter
     */
    const PARAM_PAYMENT_METHOD_TYPE = 'paymentType';

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'button/js/payment/add_method.js';

        return $list;
    }

    /**
     * Register files from common repository
     *
     * @return array
     */
    public function getCommonFiles()
    {
        $list = parent::getCommonFiles();
        $list[static::RESOURCE_JS][] = 'js/tooltip.js';

        $list[static::RESOURCE_JS][] = 'js/chosen.jquery.js';
        $list[static::RESOURCE_CSS][] = 'css/chosen/chosen.css';
        return $list;
    }

    /**
     * Define widget params
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            static::PARAM_PAYMENT_METHOD_TYPE => new \XLite\Model\WidgetParam\TypeString('Payment methods type', ''),
        );
    }

    /**
     * Return payment methods type which is provided to the widget
     *
     * @return string
     */
    protected function getPaymentType()
    {
        return $this->getParam(static::PARAM_PAYMENT_METHOD_TYPE);
    }

    /**
     * Return URL parameters to use in AJAX popup
     *
     * @return array
     */
    protected function prepareURLParams()
    {
        return array(
            'target'        => 'payment_method_selection',
            'widget'        => '\XLite\View\Payment\AddMethod',
            'paymentType'   => $this->getPaymentType(),
        );
    }

    /**
     * Return default button label
     *
     * @return string
     */
    protected function getDefaultLabel()
    {
        return 'Add payment method';
    }

    /**
     * Return CSS classes
     *
     * @return string
     */
    protected function getClass()
    {
        return parent::getClass() . ' add-payment-method-button';
    }
}
