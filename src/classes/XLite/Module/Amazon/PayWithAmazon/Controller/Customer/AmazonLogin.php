<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\Amazon\PayWithAmazon\Controller\Customer;

use XLite\Core\Auth;
use XLite\Core\Database;
use XLite\Core\Request;
use XLite\Core\TopMessage;
use XLite\Model\Profile;
use XLite\Module\Amazon\PayWithAmazon\Main;

/**
 * Amazon checkout controller
 */
class AmazonLogin extends \XLite\Controller\Customer\ACustomer
{
    protected function doNoAction()
    {
        if (Request::getInstance()->access_token) {
            $this->doActionLogin();

        } else {
            $this->setReturnURL($this->buildURL('main'));
        }
    }

    protected function doActionLogin()
    {
        $client           = Main::getClient();
        $requestProcessed = false;
        $returnURL        = '';
        $error            = '';

        $accessToken = Request::getInstance()->access_token;
        $profileInfo = [];
        try {
            $profileInfo = $client->getUserInfo($accessToken);
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }

        if ($profileInfo && !empty($profileInfo['user_id']) && !empty($profileInfo['email'])) {
            if ('loginWithAmazon' === Request::getInstance()->mode) {
                $profile = $this->getSocialLoginProfile(
                    $profileInfo['email'],
                    'Amazon',
                    $profileInfo['user_id']
                );

                if (!Auth::getInstance()->isLogged()
                    || Auth::getInstance()->getProfile()->getProfileId() !== $profile->getProfileId()
                ) {
                    if ($profile->isEnabled()) {
                        Auth::getInstance()->loginProfile($profile);

                        // We merge the logged in cart into the session cart
                        $profileCart = $this->getCart();
                        $profileCart->login($profile);
                        Database::getEM()->flush();

                        if ($profileCart->isPersistent()) {
                            $this->updateCart();
                        }

                    } else {
                        TopMessage::addError('Profile is disabled');
                        $returnURL = $this->getAuthReturnURL(true);
                    }
                }
            } else {
                if (!Auth::getInstance()->getProfile()) {
                    $this->getCart()->getProfile()->setLogin($profileInfo['email']);
                    Database::getEM()->flush();
                }
            }

            if (!$returnURL) {
                $returnURL = $this->getAuthReturnURL();
            }

            $requestProcessed = true;
        }

        if (!$requestProcessed) {
            Main::log(
                [
                    'message' => 'Error: ' . __FUNCTION__,
                    'error'   => $error ?: 'We were unable to process this request',
                ]
            );

            TopMessage::addError('We were unable to process this request');
            $returnURL = $this->getAuthReturnURL(true);
        }

        $this->setReturnURL($returnURL);
    }

    /**
     * Fetches an existing social login profile or creates new
     *
     * @param string $login          E-mail address
     * @param string $socialProvider SocialLogin auth provider
     * @param string $socialId       SocialLogin provider-unique id
     *
     * @return Profile
     */
    protected function getSocialLoginProfile($login, $socialProvider, $socialId)
    {
        $profile = Database::getRepo('XLite\Model\Profile')->findOneBy(
            [
                'socialLoginProvider' => $socialProvider,
                'socialLoginId'       => $socialId,
                'order'               => null,
            ]
        );

        if (!$profile) {
            $profile = Database::getRepo('XLite\Model\Profile')
                ->findOneBy(['login' => $login, 'order' => null, 'anonymous' => false]);
        }

        if (!$profile) {
            $profile = new Profile();
            $profile->setLogin($login);
            $profile->create();

        } elseif ($profile->isAdmin()) {
            $profile = null;
        }

        if ($profile) {
            $profile->setSocialLoginProvider($socialProvider);
            $profile->setSocialLoginId($socialId);
        }

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
        if ($failure) {

            return $this->buildURL('login');
        }

        return Request::getInstance()->returnUrl ?: $this->buildURL('amazon_checkout');
    }
}
