<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\SocialLogin\Core;

/**
 * Facebook auth provider
 */
class FacebookAuthProvider extends AAuthProvider
{
    /**
     * Unique auth provider name
     */
    const PROVIDER_NAME = 'facebook';

    /**
     * Url to which user will be redirected
     */
    const AUTH_REQUEST_URL = 'https://www.facebook.com/dialog/oauth';

    /**
     * Data to gain access to
     */
    const AUTH_REQUEST_SCOPE = 'email,user_location';

    /**
     * Url to get access token
     */
    const TOKEN_REQUEST_URL = 'https://graph.facebook.com/oauth/access_token';

    /**
     * Url to access user profile information
     */
    const PROFILE_REQUEST_URL = 'https://graph.facebook.com/me';

    /**
     * Url to access user address information
     */
    const ADDRESS_REQUEST_URL = 'https://graph.facebook.com';

    /**
     * @var string Access token object cache
     */
    protected $accessToken;

    /**
     * Returns access token based on authorization code
     *
     * @param string $code Authorization code
     *
     * @return string
     */
    protected function getAccessToken($code)
    {
        if ($this->accessToken) {
            return $this->accessToken;
        }
        $request = new \XLite\Core\HTTP\Request($this->getTokenRequestUrl($code));
        $response = $request->sendRequest();

        $this->accessToken = null;

        if (200 == $response->code) {
            parse_str($response->body, $data);
            $this->accessToken = $data['access_token'];
        }

        return $this->accessToken;
    }

    /**
     * Get profile info from auth provider
     *
     * @return array
     */
    public function processAuth()
    {
        return parent::processAuth();
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

        if (isset($profileInfo['first_name'])) {
            $address->setFirstname($profileInfo['first_name']);
        }

        if (isset($profileInfo['last_name'])) {
            $address->setLastname($profileInfo['last_name']);
        }

        if (isset($profileInfo['location']['id'])) {
            $addressInfo = $this->requestAddress($profileInfo['location']['id']);

            if (isset($addressInfo['location']['country'])) {
                $country = \XLite\Core\Database::getRepo('XLite\Model\Country')->createQueryBuilder()
                    ->andWhere('translations.country LIKE :countryName')
                    ->setParameter('countryName', '%'.$addressInfo['location']['country'] . '%')
                    ->getSingleResult();

                if ($country) {
                    $address->setCountryCode($country->getCode());

                    if (isset($addressInfo['location']['region'])) {
                        if (!$country->hasStates()) {
                            $stateName = $addressInfo['location']['region'];
                            $address->setCustomState($stateName);
                        }
                    }
                }
            }

            if (isset($addressInfo['location']['city'])) {
                $address->setCity($addressInfo['location']['city']);
            }
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

        if (isset($profileInfo['picture']['data']['url'])) {
            $picture = $profileInfo['picture']['data']['url'];
        }

        return $picture;
    }

    /**
     * Process address
     *
     * @param string $id            Address node id
     *
     * @return array
     */
    protected function requestAddress($id)
    {
        $addressinfo = array();

        $code = \XLite\Core\Request::getInstance()->code;

        if (!empty($code)) {
            $accessToken = $this->getAccessToken($code);

            if ($accessToken) {
                $request = new \XLite\Core\HTTP\Request($this->getAddressRequestUrl($id, $accessToken));
                $response = $request->sendRequest();
                if (200 == $response->code) {
                    $addressinfo = json_decode($response->body, true);
                }
            }
        }

        return $addressinfo;
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
        return static::PROFILE_REQUEST_URL . '?fields=email,location,first_name,last_name,picture{url}&access_token=' . urlencode($accessToken);
    }

    /**
     * Get url used to access user address info
     *
     * @param string $id            Address node id
     * @param string $accessToken   Access token
     *
     * @return string
     */
    protected function getAddressRequestUrl($id, $accessToken)
    {
        return static::ADDRESS_REQUEST_URL . '/' . $id . '?fields=location{country,region,city}&access_token=' . urlencode($accessToken);
    }

    /**
     * Get OAuth 2.0 client ID
     *
     * @return string
     */
    protected function getClientId()
    {
        return \XLite\Core\Config::getInstance()->CDev->SocialLogin->fb_client_id;
    }

    /**
     * Get OAuth 2.0 client secret
     *
     * @return string
     */
    protected function getClientSecret()
    {
        return \XLite\Core\Config::getInstance()->CDev->SocialLogin->fb_client_secret;
    }
}
