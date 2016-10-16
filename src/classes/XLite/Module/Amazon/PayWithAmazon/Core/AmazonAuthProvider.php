<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\Amazon\PayWithAmazon\Core;

/**
 * Google auth provider
 */
class AmazonAuthProvider extends \XLite\Module\CDev\SocialLogin\Core\AAuthProvider
{
    /**
     * Unique auth provider name
     */
    const PROVIDER_NAME = 'amazon';

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
        return \Xlite\Core\Config::getInstance()->Amazon->PayWithAmazon->amazon_pa_client_id;
    }

    /**
     * Get OAuth 2.0 client secret
     *
     * @return string
     */
    protected function getClientSecret()
    {
        return true;
    }

    /**
     * Check if auth provider has all options configured
     *
     * @return boolean
     */
    public function isConfigured()
    {
        $api = \XLite\Module\Amazon\PayWithAmazon\Main::getApi();

        return parent::isConfigured() && $api->isConfigured();
    }
}
