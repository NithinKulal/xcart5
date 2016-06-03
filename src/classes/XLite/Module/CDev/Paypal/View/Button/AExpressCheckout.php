<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\View\Button;

/**
 * Express Checkout base button
 */
abstract class AExpressCheckout extends \XLite\View\Button\Link
{
    const PARAM_IN_CONTEXT = 'inContext';

    /**
     * Returns true if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        $cart = $this->getCart();

        return parent::isVisible()
            && \XLite\Module\CDev\Paypal\Main::isExpressCheckoutEnabled($cart);
    }

    /**
     * Get CSS class name
     *
     * @return string
     */
    protected function getClass()
    {
        return 'pp-ec-button';
    }

    /**
     * Get merchant id
     *
     * @return string
     */
    protected function getMerchantId()
    {
        return \XLite\Module\CDev\Paypal\Main::getMerchantId();
    }

    /**
     * Check for merchant id is present
     *
     * @return boolean
     */
    protected function hasMerchantId()
    {
        return (bool) $this->getMerchantId();
    }

    /**
     * defineWidgetParams
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams[static::PARAM_LOCATION] = new \XLite\Model\WidgetParam\TypeString(
            'Redirect to',
            $this->buildURL('checkout', 'start_express_checkout')
        );

        $this->widgetParams[static::PARAM_IN_CONTEXT] = new \XLite\Model\WidgetParam\TypeBool(
            'Is In-Context checkout',
            $this->defineInContext()
        );
    }

    /**
     * Returns additional link params
     *
     * @return array
     */
    protected function getAdditionalLinkParams()
    {
        return array(
            'ignoreCheckout'    => false
        );
    }

    /**
     * Define inContext widget param
     *
     * @return boolean
     */
    protected function defineInContext()
    {
        return \XLite\Module\CDev\Paypal\Main::isInContextCheckoutAvailable();
    }

    /**
     * Check if In-Context checkout available
     *
     * @return boolean
     */
    protected function isInContextAvailable()
    {
        return $this->getParam(static::PARAM_IN_CONTEXT);
    }

    /**
     * We make the full location path for the provided URL
     *
     * @return string
     */
    protected function getLocationURL()
    {
        $url = $this->getParam(static::PARAM_LOCATION);

        $params = $this->getAdditionalLinkParams();
        if ($params) {
            $url = $this->buildURL('checkout', 'start_express_checkout', $params);
        }

        return \XLite::getInstance()->getShopURL(
            $url,
            \XLite\Core\Config::getInstance()->Security->customer_security
        );
    }
}
