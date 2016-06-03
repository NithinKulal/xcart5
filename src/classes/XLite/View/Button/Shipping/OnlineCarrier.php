<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Button\Shipping;

/**
 * Online carrier popup link
 */
class OnlineCarrier extends \XLite\View\Button\Link
{
    const PARAM_METHOD = 'method';

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
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'button/shipping/online_carrier.twig';
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
     * Return CSS classes
     *
     * @return string
     */
    protected function getClass()
    {
        return parent::getClass() . ' add-shipping-carrier-link';
    }

    /**
     * Return button text
     *
     * @return string
     */
    protected function getButtonLabel()
    {
        return $this->getMethod()->getProcessorObject()->getProcessorName();
    }

    /**
     * We make the full location path for the provided URL
     *
     * @return string
     */
    protected function getLocationURL()
    {
        $method = $this->getMethod();

        return $method->getProcessorObject()->getSettingsURL();
    }
}
