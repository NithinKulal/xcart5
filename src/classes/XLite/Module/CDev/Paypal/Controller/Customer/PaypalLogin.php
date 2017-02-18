<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\Controller\Customer;

/**
 * Paypal login controller
 */
class PaypalLogin extends \XLite\Controller\Customer\ACustomer
{
    /**
     * Perform login action
     *
     * @return void
     */
    protected function doActionLogin()
    {
        $loginApi = new \XLite\Module\CDev\Paypal\Core\Login();
        $requestProcessed = false;
        $returnURL = '';

        \XLite\Module\CDev\Paypal\Main::addLog(
            'Login return',
            \XLite\Core\Request::getInstance()->getData()
        );

        if ($loginApi->checkRequest()) {

            $accessToken = $loginApi->createFromAuthorisationCode(\XLite\Core\Request::getInstance()->code);

            $profileInfo = isset($accessToken['access_token'])
                ? $loginApi->getUserinfo($accessToken['access_token'])
                : null;

            if ($profileInfo && !empty($profileInfo['user_id']) && !empty($profileInfo['email'])) {

                $profile = $this->getSocialLoginProfile(
                    $profileInfo['email'],
                    'PayPal',
                    $profileInfo['user_id'],
                    $profileInfo
                );

                if ($profile) {
                    if ($profile->isEnabled()) {
                        \XLite\Core\Auth::getInstance()->loginProfile($profile);

                        $accessToken['expirationTime'] = LC_START_TIME + $accessToken['expires_in'];
                        \XLite\Core\Session::getInstance()->paypalAccessToken = $accessToken;

                        // We merge the logged in cart into the session cart
                        $profileCart = $this->getCart();
                        $profileCart->login($profile);
                        \XLite\Core\Database::getEM()->flush();

                        if ($profileCart->isPersistent()) {
                            $this->updateCart();
                        }

                        $returnURL = $this->getAuthReturnURL();

                    } else {
                        \XLite\Core\TopMessage::addError('Profile is disabled');
                        $returnURL = $this->getAuthReturnURL(true);
                    }

                } else {
                    $provider = \XLite\Core\Database::getRepo('XLite\Model\Profile')
                        ->findOneBy(array('login' => $profileInfo['email'], 'order' => null))
                        ->getSocialLoginProvider();

                    if ($provider) {
                        $signInVia = 'Please sign in with ' . $provider . '.';
                    } else {
                        $signInVia = 'Profile with the same e-mail address already registered. '
                            . 'Please sign in the classic way.';
                    }

                    \XLite\Core\TopMessage::addError($signInVia);
                    $returnURL = $this->getAuthReturnURL(true);
                }

                $requestProcessed = true;
            }
        }

        if (!$requestProcessed) {
            \XLite\Core\TopMessage::addError('We were unable to process this request');
            $returnURL = '';
        }

        $this->closePopup($returnURL);
    }

    /**
     * Fetches an existing social login profile or creates new
     *
     * @param string $login          E-mail address
     * @param string $socialProvider SocialLogin auth provider
     * @param string $socialId       SocialLogin provider-unique id
     * @param array  $profileInfo    Profile info OPTIONAL
     *
     * @return \XLite\Model\Profile
     */
    protected function getSocialLoginProfile($login, $socialProvider, $socialId, $profileInfo = array())
    {
        $profile = \XLite\Core\Database::getRepo('XLite\Model\Profile')->findOneBy(
            array(
                'socialLoginProvider'   => $socialProvider,
                'socialLoginId'         => $socialId,
                'order'              => null,
            )
        );

        if (!$profile) {
            $profile = \XLite\Core\Database::getRepo('XLite\Model\Profile')
                ->findOneBy(array('login' => $login, 'order' => null));
        }

        if (!$profile) {
            $profile = new \XLite\Model\Profile();
            $profile->setLogin($login);
            $profile->create();

            if (
                $profileInfo
                && isset($profileInfo['given_name'])
                && isset($profileInfo['family_name'])
                && isset($profileInfo['address'])
            ) {
                $address = new \XLite\Model\Address();
                $address->setProfile($profile);

                $address->setFirstname($profileInfo['given_name']);
                $address->setLastname($profileInfo['family_name']);

                if (
                    isset($profileInfo['address']['country'])
                    && isset($profileInfo['address']['region'])
                ) {
                    $address->setCountryCode($profileInfo['address']['country']);

                    $state = \XLite\Core\Database::getRepo('XLite\Model\State')->findOneByCountryAndState(
                        $profileInfo['address']['country'],
                        $profileInfo['address']['region']
                    );

                    if ($state) {
                        $address->setState($state);
                    }
                }

                if (isset($profileInfo['address']['locality'])) {
                    $address->setCity($profileInfo['address']['locality']);
                }

                if (isset($profileInfo['address']['street_address'])) {
                    $address->setStreet($profileInfo['address']['street_address']);
                }

                if (isset($profileInfo['address']['postal_code'])) {
                    $address->setZipcode($profileInfo['address']['postal_code']);
                }

                if (isset($profileInfo['phone_number'])) {
                    $address->setPhone($profileInfo['phone_number']);
                }

                $address->setIsShipping(true);
                $address->setIsBilling(true);

                $profile->addAddresses($address);
                $address->create();
            }
        }

        $profile->setSocialLoginProvider($socialProvider);
        $profile->setSocialLoginId($socialId);

        return $profile;
    }

    /**
     * Set redirect URL
     *
     * @param mixed $failure Indicates if auth process failed OPTIONAL
     *
     * @return string
     */
    protected function getAuthReturnURL($failure = false)
    {
        $state = \XLite\Core\Request::getInstance()->{\XLite\Module\CDev\Paypal\Core\PaypalAuthProvider::STATE_PARAM_NAME};
        $state = $state
            ? explode(\XLite\Module\CDev\SocialLogin\Core\AAuthProvider::STATE_DELIMITER, urldecode($state))
            : [];
        $controller = isset($state[0]) ? $state[0] : null;
        $returnURL = isset($state[1]) ? $state[1] : null;
        $redirectTo = $failure ? 'login' : '';

        if ('XLite\Controller\Customer\Checkout' === $controller) {
            $redirectTo = 'checkout';
        } elseif ('XLite\Controller\Customer\Profile' === $controller) {
            $redirectTo = 'profile';
        }

        if (empty($redirectTo) && $returnURL && $this->checkReturnUrl($returnURL)) {
            return \Includes\Utils\URLManager::getShopURL(urldecode($returnURL));
        }

        return $this->buildURL($redirectTo);
    }

    /**
     * Check if return url is relative or
     *
     * @param $url
     *
     * @return bool
     */
    protected function checkReturnUrl($url) {
        if (preg_match("#^https?://([^/]+)/#", $url, $matches)) {
            return in_array(
                $matches[1],
                $this->getStoreDomains()
            );
        }

        return !preg_match('/^https?:\/\//Ss', $url);
    }

    /**
     * Return store allowed domains
     *
     * @return array
     */
    protected function getStoreDomains()
    {
        $domains = explode(',', \XLite\Core\ConfigParser::getOptions(['host_details', 'domains']));
        $domains[] = \XLite\Core\ConfigParser::getOptions(['host_details', 'http_host']);
        $domains[] = \XLite\Core\ConfigParser::getOptions(['host_details', 'https_host']);

        return array_unique(array_filter($domains));
    }

    // {{{ Popup related methods

    /**
     * Preprocessor for no-action run
     *
     * @return void
     */
    protected function doNoAction()
    {
        $api = new \XLite\Module\CDev\Paypal\Core\Login();
        $url = $api->getSignInURL();

        $this->redirect($url);
    }

    /**
     * Close Paypal login popup
     *
     * @param string $returnURL Return URL OPTIONAL
     *
     * @return void
     */
    protected function closePopup($returnURL = '')
    {
        if ($returnURL) {

            $returnURL = $this->getShopURL($returnURL);

            echo (
            <<<HTML
    <script type="text/javascript">
    window.opener.location.replace("{$returnURL}");
    window.close();
</script>
HTML
            );

        } else {
            echo (
            <<<HTML
    <script type="text/javascript">
    window.opener.location.reload();
    window.close();
</script>
HTML
            );
        }

        exit ();
    }

    // }}}
}
