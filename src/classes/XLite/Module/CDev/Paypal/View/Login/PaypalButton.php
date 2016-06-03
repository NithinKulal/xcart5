<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\View\Login;

/**
 * Facebook sign-in button
 *
 * @Decorator\Depend ("CDev\SocialLogin")
 * @ListChild (list="social.login.buttons", zone="customer", weight="20")
 */
class PaypalButton extends \XLite\Module\CDev\SocialLogin\View\AButton
{
    /**
     * Widget display name
     */
    const DISPLAY_NAME = 'PayPal';

    /**
     * Font awesome class
     */
    const FONT_AWESOME_CLASS = 'fa-paypal';

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/CDev/Paypal/login/style.css';

        return $list;
    }

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'modules/CDev/Paypal/login/controller.js';

        return $list;
    }

    /**
     * Get authentication request url
     *
     * @return string
     */
    public function getAuthRequestUrl()
    {
        return $this->buildURL(
            'paypal_login',
            '',
            array('state' => get_class(\XLite::getController()))
        );
    }

    /**
     * Returns an instance of auth provider
     *
     * @return \XLite\Module\CDev\SocialLogin\Core\AAuthProvider
     */
    protected function getAuthProvider()
    {
        return \XLite\Module\CDev\Paypal\Core\PaypalAuthProvider::getInstance();
    }

    /**
     * Return default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/CDev/Paypal/login/button.twig';
    }
}
