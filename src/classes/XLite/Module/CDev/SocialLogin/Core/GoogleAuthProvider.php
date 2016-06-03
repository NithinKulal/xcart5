<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\SocialLogin\Core;

/**
 * Google auth provider
 */
class GoogleAuthProvider extends AAuthProvider
{
    /**
     * Unique auth provider name
     */
    const PROVIDER_NAME = 'google';

    /**
     * Url to which user will be redirected
     */
    const AUTH_REQUEST_URL = 'https://accounts.google.com/o/oauth2/auth';

    /**
     * Data to gain access to
     */
    const AUTH_REQUEST_SCOPE = 'https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fuserinfo.email+https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fuserinfo.profile';

    /**
     * Url to get access token
     */
    const TOKEN_REQUEST_URL = 'https://accounts.google.com/o/oauth2/token';

    /**
     * Url to access user profile information
     */
    const PROFILE_REQUEST_URL = 'https://www.googleapis.com/oauth2/v1/userinfo';

    /**
     * Process authorization grant and return array with profile data
     *
     * @return array Client information containing at least id and e-mail
     */
    public function processAuth()
    {
        $profile = parent::processAuth();

        if (isset($profile['email'])) {
            $profile['id'] = $profile['email'];
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
        $address = \XLite\Model\Address::createDefaultShippingAddress();

        $address->setIsShipping(true);
        $address->setIsBilling(true);
        $address->setIsWork(false);

        if (isset($profileInfo['given_name'])) {
            $address->setFirstname($profileInfo['given_name']);
        }

        if (isset($profileInfo['family_name'])) {
            $address->setLastname($profileInfo['family_name']);
        }

        return $address;
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
        $picture = null;

        if (isset($profileInfo['picture'])) {
            $picture = $profileInfo['picture'];
        }

        return $picture;
    }

    /**
     * Returns access token based on authorization code
     *
     * @param string $code Authorization code
     *
     * @return string
     */
    protected function getAccessToken($code)
    {
        $request = new \XLite\Core\HTTP\Request(static::TOKEN_REQUEST_URL);
        $request->body = array(
            'code'          => $code,
            'client_id'     => $this->getClientId(),
            'client_secret' => $this->getClientSecret(),
            'redirect_uri'  => $this->getRedirectUrl(),
            'grant_type'    => 'authorization_code',
        );

        $response = $request->sendRequest();

        $accessToken = null;
        if (200 == $response->code) {
            $data = json_decode($response->body, true);
            $accessToken = $data['access_token'];
        }

        return $accessToken;
    }

    /**
     * Get OAuth 2.0 client ID
     *
     * @return string
     */
    protected function getClientId()
    {
        return \XLite\Core\Config::getInstance()->CDev->SocialLogin->gg_client_id;
    }

    /**
     * Get OAuth 2.0 client secret
     *
     * @return string
     */
    protected function getClientSecret()
    {
        return \XLite\Core\Config::getInstance()->CDev->SocialLogin->gg_client_secret;
    }
}
