<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Payment;

/**
 * Payment configuration page
 */
class MethodStatus extends \XLite\View\AView
{
    const PARAM_METHOD = 'method';

    /**
     * Get css files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'payment/method_status/style.css';

        return $list;
    }

    /**
     * Get js files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'payment/method_status/controller.js';

        return $list;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'payment/method_status/body.twig';
    }

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            self::PARAM_METHOD => new \XLite\Model\WidgetParam\TypeObject(
                'Payment method',
                $this->getDefaultPaymentMethod(),
                false,
                '\XLite\Model\Payment\Method'
            ),
        );
    }

    /**
     * Return default payment method
     *
     * @return \XLite\Model\Payment\Method
     */
    protected function getDefaultPaymentMethod()
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Payment\Method')
            ->find(\XLite\Core\Request::getInstance()->method_id);
    }

    /**
     * Return current payment method
     *
     * @return \XLite\Model\Payment\Method
     */
    protected function getPaymentMethod()
    {
        return $this->getParam(static::PARAM_METHOD);
    }

    /**
     * Return current payment method id
     *
     * @return int
     */
    protected function getMethodId()
    {
        return $this->getPaymentMethod() ? $this->getPaymentMethod()->getMethodId() : null;
    }

    /**
     * Check visibility
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && $this->getPaymentMethod();
    }

    /**
     * Returns style class
     *
     * @return string
     */
    protected function getClass()
    {
        return $this->isEnabled()
            ? 'alert alert-success'
            : 'alert alert-warning';
    }

    /**
     * Check if method is enabled
     *
     * @return boolean
     */
    protected function isEnabled()
    {
        $method = $this->getPaymentMethod();

        return $method && $method->isEnabled();
    }

    /**
     * Check if method is disabled
     *
     * @return boolean
     */
    protected function isDisabled()
    {
        return !$this->isEnabled();
    }

    /**
     * Get message why we can't switch payment method
     *
     * @return string
     */
    public function getNotSwitchableReason()
    {
        $method = $this->getPaymentMethod();

        return $method
            ? $method->getNotSwitchableReason()
            : static::t('This payment method is not configured.');
    }

    /**
     * Check if method status is switchable
     *
     * @return boolean
     */
    protected function isSwitchable()
    {
        $method = $this->getPaymentMethod();

        return $method && $method->isEnabled() ? !$method->isForcedEnabled() : $method->canEnable();
    }

    /**
     * Returns processor code
     *
     * @return string
     */
    protected function getProcessor()
    {
        $method = $this->getPaymentMethod();

        return $method
            ? $method->getProcessor()
            : '';
    }

    /**
     * Returns 'before' list name (with payment method service name)
     *
     * @return string
     */
    protected function getBeforeListName()
    {
        $serviceName = $this->getPaymentMethod()->getServiceName();

        return 'payment_status.before.' . preg_replace('/[^\w]/', '_', $serviceName);
    }

    /**
     * Returns 'after' list name (with payment method service name)
     *
     * @return string
     */
    protected function getAfterListName()
    {
        $serviceName = $this->getPaymentMethod()->getServiceName();

        return 'payment_status.after.' . preg_replace('/[^\w]/', '_', $serviceName);
    }
}
