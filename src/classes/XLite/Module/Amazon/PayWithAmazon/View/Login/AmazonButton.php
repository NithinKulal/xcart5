<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\Amazon\PayWithAmazon\View\Login;

use XLite\Module\Amazon\PayWithAmazon\Core\AmazonAuthProvider;

/**
 * Amazon sign-in button
 *
 * @Decorator\Depend ("CDev\SocialLogin")
 * @ListChild (list="social.login.buttons", zone="customer", weight="20")
 */
class AmazonButton extends \XLite\Module\CDev\SocialLogin\View\AButton
{
    /**
     * Widget display name
     */
    const DISPLAY_NAME = 'Amazon';

    /**
     * Font awesome class
     */
    const FONT_AWESOME_CLASS = 'fa-amazon';

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list   = parent::getCSSFiles();
        $list[] = 'modules/Amazon/PayWithAmazon/login/style.css';

        return $list;
    }

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list   = parent::getJSFiles();
        $list[] = 'modules/Amazon/PayWithAmazon/login/controller.js';

        return $list;
    }

    /**
     * Get authentication request url
     *
     * @return string
     */
    public function getAuthRequestUrl()
    {
        return $this->buildURL('amazon_login');
    }

    /**
     * Returns an instance of auth provider
     *
     * @return \XLite\Module\CDev\SocialLogin\Core\AAuthProvider
     */
    protected function getAuthProvider()
    {
        return AmazonAuthProvider::getInstance();
    }

    /**
     * Return default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/Amazon/PayWithAmazon/login/button.twig';
    }

    /**
     * @return string
     */
    protected function getHash()
    {
        return md5(microtime());
    }
}
