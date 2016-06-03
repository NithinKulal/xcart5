<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\SocialLogin\Core;

/**
 * Auth provider abstract class
 */
abstract class AAuthProvider extends \XLite\Base\Singleton
{
    /**
     * Authorization grant provider param name
     */
    const AUTH_PROVIDER_PARAM_NAME = 'auth_provider';

    /**
     * State parameter is used to maintain state between the request and callback
     */
    const STATE_PARAM_NAME = 'state';

    /**
     * Get OAuth 2.0 client ID
     *
     * @return string
     */
    abstract protected function getClientId();

    /**
     * Get OAuth 2.0 client secret
     *
     * @return string
     */
    abstract protected function getClientSecret();

    /**
     * Get authorization request url
     *
     * @param string $state State parameter to include in request
     *
     * @return string
     */
    public function getAuthRequestUrl($state)
    {
        return static::AUTH_REQUEST_URL
            . '?client_id=' . $this->getClientId()
            . '&redirect_uri=' . urlencode($this->getRedirectUrl())
            . '&scope=' . static::AUTH_REQUEST_SCOPE
            . '&response_type=code'
            . '&' . static::STATE_PARAM_NAME . '=' . urlencode($state);
    }

    /**
     * Get unique auth provider name to distinguish it from others
     *
     * @return string
     */
    public function getName()
    {
        return static::PROVIDER_NAME;
    }

    /**
     * Check if current request belongs to the concrete implementation of auth provider
     *
     * @return boolean
     */
    public function detectAuth()
    {
        return \XLite\Core\Request::getInstance()->{static::AUTH_PROVIDER_PARAM_NAME} == $this->getName();
    }

    /**
     * Process authorization grant and return array with profile data
     *
     * @return array Client information containing at least id and e-mail
     */
    public function processAuth()
    {
        $profile = array();

        $code = \XLite\Core\Request::getInstance()->code;

        if (!empty($code)) {
            $accessToken = $this->getAccessToken($code);

            if ($accessToken) {
                $request = new \XLite\Core\HTTP\Request($this->getProfileRequestUrl($accessToken));
                $response = $request->sendRequest();

                if (200 == $response->code) {
                    $profile = json_decode($response->body, true);
                }
            }
        }

        return $profile;
    }

    /**
     * Get address from auth provider
     *
     * @param array $profileInfo Previous request result
     *
     * @return \XLite\Model\Address
     */
    public function processAddress($profileInfo)
    {
        return null;
    }

    /**
     * Get picture from auth provider
     *
     * @param array $profileInfo Previous request result
     *
     * @return string
     */
    public function processPicture($profileInfo)
    {
        return null;
    }

    /**
     * Check if auth provider has all options configured
     *
     * @return boolean
     */
    public function isConfigured()
    {
        return $this->getClientId() && $this->getClientSecret();
    }

    /**
     * Get url to request access token
     *
     * @param string $code Authorization code
     *
     * @return string
     */
    protected function getTokenRequestUrl($code)
    {
        return static::TOKEN_REQUEST_URL
            . '?client_id=' . $this->getClientId()
            . '&redirect_uri=' . urlencode($this->getRedirectUrl())
            . '&client_secret=' . $this->getClientSecret()
            . '&code=' . urlencode($code);
    }

    /**
     * Get url used to access user profile info
     *
     * @param string $accessToken Access token
     *
     * @return string
     */
    protected function getProfileRequestUrl($accessToken)
    {
        return static::PROFILE_REQUEST_URL . '?access_token=' . urlencode($accessToken);
    }

    /**
     * Get authorization grant redirect url
     *
     * @return string
     */
    protected function getRedirectUrl()
    {
        return \XLite\Core\URLManager::getShopURL(
            \XLite\Core\Converter::buildURL(
                'social_login',
                'login',
                array('auth_provider' => $this->getName()),
                'cart.php'
            ),
            \XLite\Core\Request::getInstance()->isHTTPS(),
            array(),
            null,
            false
        );
    }
}
