<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\Core;

/**
 * RESTAPI
 *
 * @see https://developer.paypal.com/docs/api/
 */
class RESTAPI extends \XLite\Module\CDev\Paypal\Core\AAPI
{
    /**
     * API credentials
     * (vab@x-cart.com)
     */
    const CLIENT_ID     = 'AWTzQBBzsLZufFGNl_oWWdzM7BqB27aLXw2SRUYGb4U-Qi104Db5tF0OPnRg';
    const CLIENT_SECRET = 'EEQdxRAZua_WrdOeY9Yl5vzraRwDerCMSkHoc_q89PAArqK6Gs8kcXQT5Weq';
    const PARTNER_ID    = 'FWY6M72RRKFGW';

    // {{{ Common

    /**
     * Constructor
     */
    public function __construct()
    {
    }

    // }}}

    // {{{ Config

    /**
     * Is test mode
     *
     * @return boolean
     */
    public function isTestMode()
    {
        return false;
    }

    // }}}

    // {{{ Sign up

    /**
     * Is In-Context Boarding SignUp available
     *
     * @return boolean
     */
    public function isInContextSignUpAvailable()
    {
        return in_array(
            \XLite\Core\Config::getInstance()->Company->location_country,
            array('US', 'UK')
        );
    }

    /**
     * Get SignUp url
     *
     * @param string $returnUrl Return url
     *
     * @return string
     */
    public function getSignUpUrl($returnUrl)
    {
        $urlParams = array(
            // Your secure merchant account ID, also known as your payer ID. This
            // can be obtained from your PayPal account.
            'partnerId' => static::PARTNER_ID,

            // The product you want the merchant to enroll for. At this time, only
            // the values shown below are supported; however, as this product
            // matures, additional products will be added to this list.
            // - addipmt (Express Checkout)
            // - wp_pro (PayPal Payments Pro)
            'productIntentID' => 'addipmt',

            // The merchant’s country of residence.
            // - US
            // - UK
            'countryCode' => \XLite\Core\Config::getInstance()->Company->location_country,

            // Indicates how you intend to display Integrated PayPal Signup to the
            // merchant. This can be either regular or minibrowser. (See
            // section X.X for information on displaying Integrated PayPal Signup
            // to the merchant in a minibrowser.)
            'displayMode' => 'minibrowser',

            // Indicates whether you are requesting first-party API credentials (F)
            // or third party API permissions (T).
            'integrationType' => 'F',

            // If you are requesting first-party API credentials, this indicates
            // whether you are requesting credentials that contain an API
            // signature (S) or an API certificate (C).
            // Note: If you are retrieving the merchant’s API credentials through
            // the REST API (see section X.X), the API currently only supports
            // retrieval of credentials that contain an API signature.
            'subIntegrationType' => 'S',

            // A comma-separated list of API permissions that need to be granted
            // from the merchant’s account to yours. This field is only required if
            // you are requesting third party permissions from the merchant.
            'permissionNeeded' => 'EXPRESS_CHECKOUT,REFUND,AUTH_CAPTURE',

            // URL where PayPal will return the merchant after they have
            // completed the signup flow on PayPal.
            'returnToPartnerUrl' => urlencode($returnUrl),

            // Indicates whether you want to receive the merchant’s API
            // credentials (TRUE) or whether you want PayPal to simply display
            // them to the merchant (FALSE). If you are requesting third-party
            // permissions to the merchant’s account, set this to FALSE. If you
            // do not specify a value for this field, PayPal does not provision the
            // merchant with API credentials.
            'receiveCredentials' => 'TRUE',

            // Indicates whether you need the merchant to grant third-party API
            // permissions to you. If you are signing up a merchant from your
            // front-of-site, you should generally set this to FALSE.
            'showPermissions' => 'FALSE',

            'productSelectionNeeded' => 'FALSE',

            // A unique identifier that you generate for the merchant. This will be
            // passed back to you when the merchant returns to you from PayPal.
            // If you are requesting first-party API credentials for the merchant
            // (e.g., you set receiveCredentials to TRUE), you will use this
            // identifier to request API credentials from PayPal later.
            'merchantId' => 'TEST0001',

            'partnerLogoUrl' => urlencode(\XLite\Module\CDev\Paypal\Main::getSignUpLogo()),
        );

        return 'https://www.paypal.com/webapps/merchantboarding/webflow/externalpartnerflow'
        . '?&'
        . http_build_query($urlParams);
    }

    // }}}

    // {{{ Access token

    /**
     * Retrieve access token
     * todo: caching
     *
     * @param string $clientId     Client id
     * @param string $clientSecret Client secret
     *
     * @return mixed
     * @see    https://developer.paypal.com/docs/api/#authentication--headers
     */
    public function getAccessToken($clientId, $clientSecret)
    {
        $params = array($clientId, $clientSecret);

        return $this->doRequest('accessToken', $params);
    }

    /**
     * Prepare url
     *
     * @param string $url    Request url
     * @param array  $params Request params
     *
     * @return string
     * @see    https://developer.paypal.com/docs/api/#authentication--headers
     */
    protected function prepareAccessTokenUrl($url, $params)
    {
        return $url . '/v1/oauth2/token';
    }

    /**
     * Prepare body
     *
     * @param array $params Request params
     *
     * @return string
     * @see    https://developer.paypal.com/docs/api/#authentication--headers
     */
    protected function prepareAccessTokenParams($params)
    {
        return array('grant_type' => 'client_credentials');
    }

    /**
     * Prepare request object
     *
     * @param \XLite\Core\HTTP\Request $request Request object
     * @param array                    $params  Request params
     *
     * @return \XLite\Core\HTTP\Request
     * @see    https://developer.paypal.com/docs/api/#authentication--headers
     */
    protected function prepareAccessTokenRequest($request, $params)
    {
        $request->setHeader('Accept', 'application/json');
        $request->setHeader('Accept-Language', 'en_US');
        $request->setHeader('Content-Type', 'application/x-www-form-urlencoded');
        $request->setHeader('Authorization', sprintf('Basic %s', base64_encode(implode(':', $params))));

        return $request;
    }

    // }}}

    // {{{ Merchant credentials

    /**
     * Retrieve merchant credentials
     *
     * @param string $partnerId  Partner id
     * @param string $merchantId Merchant id
     *
     * @return mixed
     */
    public function getMerchantCredentials($partnerId, $merchantId)
    {
        $data = array($partnerId, $merchantId);

        return $this->doRequest('merchantCredentials', $data);
    }

    /**
     * Prepare url
     *
     * @param string $url    Request url
     * @param array  $params Request params
     *
     * @return string
     * @see    https://developer.paypal.com/docs/api/#authentication--headers
     */
    protected function prepareMerchantCredentialsUrl($url, $params)
    {
        list($partnerId, $merchantId) = $params;

        return sprintf('%s/v1/customer/partners/%s/merchant-integrations/%s', $url, $partnerId, $merchantId);
    }

    /**
     * Prepare request object
     *
     * @param \XLite\Core\HTTP\Request $request Request object
     * @param array                    $params  Request params
     *
     * @return \XLite\Core\HTTP\Request
     */
    protected function prepareMerchantCredentialsRequest($request, $params)
    {
        $request->verb = 'GET';

        $accessToken = $this->getAccessToken(static::CLIENT_ID, static::CLIENT_SECRET);
        if (is_array($accessToken) && $accessToken['access_token']) {
            $request->setHeader('Authorization', sprintf('Bearer %s', $accessToken['access_token']));
        }

        return $request;
    }

    /**
     * Prepare body
     *
     * @param array $params Request params
     *
     * @return string
     */
    protected function prepareMerchantCredentialsParams($params)
    {
        return array();
    }

    // }}}

    // {{{ Backend request

    /**
     * Prepare url
     *
     * @param string $url    Url
     * @param string $type   Request type
     * @param array  $params Request params
     *
     * @return string
     */
    protected function prepareUrl($url, $type, $params)
    {
        $url = $this->isTestMode()
            ? 'https://api.sandbox.paypal.com'
            : 'https://api.paypal.com';

        return parent::prepareUrl($url, $type, $params);
    }

    /**
     * Returns parsed response
     *
     * @param string $type Response type
     * @param string $body Response body
     *
     * @return array
     */
    protected function parseResponse($type, $body)
    {
        return json_decode($body, true);
    }
}
