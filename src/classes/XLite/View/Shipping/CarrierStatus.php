<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Shipping;

/**
 * Online carrier status
 */
class CarrierStatus extends \XLite\View\AView
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
        $list[] = 'shipping/carrier_status/style.css';

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
        $list[] = 'shipping/carrier_status/controller.js';

        return $list;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'shipping/carrier_status/body.twig';
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
            static::PARAM_METHOD
                => new \XLite\Model\WidgetParam\TypeObject('Method', null, false, 'XLite\Model\Shipping\Method'),
        );
    }

    /**
     * Returns shipping method object
     *
     * @return \XLite\Model\Shipping\Method
     */
    protected function getMethod()
    {
        return $this->getParam(static::PARAM_METHOD);
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
        $method = $this->getMethod();

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
     * Check if method status is switchable
     *
     * @return boolean
     */
    protected function isSwitchable()
    {
        $method = $this->getMethod();

        return $method && $method->getProcessorObject() && $method->getProcessorObject()->isConfigured();
    }

    /**
     * Returns sign up URL
     *
     * @return string
     */
    protected function getSignUpURL()
    {
        $method = $this->getMethod();

        return $method && $method->getProcessorObject()
            ? $this->getMethod()->getProcessorObject()->getSignUpURL()
            : '';
    }

    /**
     * Returns processor code
     *
     * @return string
     */
    protected function getProcessor()
    {
        $method = $this->getMethod();

        return $method
            ? $method->getProcessor()
            : '';
    }
}
