<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\View\Button;

/**
 * Sign up button
 */
class SignUp extends \XLite\View\Button\SimpleLink
{
    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/CDev/Paypal/settings/signup.css';

        return $list;
    }

    /**
     * Get a list of JavaScript files required to display the widget properly
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $api = \XLite\Module\CDev\Paypal\Main::getRESTAPIInstance();
        if ($api->isInContextSignUpAvailable()) {
            $list[] = 'modules/CDev/Paypal/settings/signup.js';
        }

        return $list;
    }

    /**
     * Get CSS class name
     *
     * @return string
     */
    protected function getClass()
    {
        return parent::getClass() . 'btn regular-button pp-signup';
    }

    /**
     * Defines the default location path
     *
     * @return string
     */
    protected function getDefaultLocation()
    {
        $api = \XLite\Module\CDev\Paypal\Main::getRESTAPIInstance();
        $method = \XLite\Module\CDev\Paypal\Main::getPaymentMethod(\XLite\Module\CDev\Paypal\Main::PP_METHOD_EC);

        return $api->isInContextSignUpAvailable()
            ? $method->getReferralPageURL($method)
            : $this->buildURL('paypal_settings', '', array('method_id' => $method->getMethodId()));
    }

    /**
     * Get default attributes
     *
     * @return array
     */
    protected function getDefaultAttributes()
    {
        $params = array(
            'target'             => 'PPFrame',
            'data-paypal-button' => 'true',
        );

        $api = \XLite\Module\CDev\Paypal\Main::getRESTAPIInstance();

        return $api->isInContextSignUpAvailable()
            ? $params
            : array();
    }
}
