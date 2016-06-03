<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\Core;

/**
 * Google auth provider
 */
class PaypalAuthProvider extends \XLite\Module\CDev\SocialLogin\Core\AAuthProvider
{
    /**
     * Unique auth provider name
     */
    const PROVIDER_NAME = 'paypal';

    /**
     * Url to which user will be redirected
     */
    const AUTH_REQUEST_URL = '';

    /**
     * Data to gain access to
     */
    const AUTH_REQUEST_SCOPE = '';

    /**
     * Url to get access token
     */
    const TOKEN_REQUEST_URL = '';

    /**
     * Url to access user profile information
     */
    const PROFILE_REQUEST_URL = '';

    /**
     * Get OAuth 2.0 client ID
     *
     * @return string
     */
    protected function getClientId()
    {
        return \Xlite\Core\Config::getInstance()->CDev->Paypal->loginClientId;
    }

    /**
     * Get OAuth 2.0 client secret
     *
     * @return string
     */
    protected function getClientSecret()
    {
        return \Xlite\Core\Config::getInstance()->CDev->Paypal->loginClientSecret;
    }
}
