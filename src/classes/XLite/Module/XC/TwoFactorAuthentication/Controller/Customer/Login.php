<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\TwoFactorAuthentication\Controller\Customer;

/**
 * Login page controller
 */
class Login extends \XLite\Controller\Customer\Login implements \XLite\Base\IDecorator
{
    /**
     * Check if alternative login body used
     *
     * @return boolean
     */
    protected function isAlternativeLoginUsed()
    {
        $isUses = false;

        $data = \XLite\Core\Request::getInstance()->getData();

        list($profile, $result) = \XLite\Core\Auth::getInstance()->checkLoginPassword($data['login'], $data['password']);

        if (isset($profile) && $result === true) {
            $isAdmin = \XLite\Core\Auth::getInstance()->isAdmin($profile)
                && \XLite\Core\Config::getInstance()->XC->TwoFactorAuthentication->admin_interface;

            if (
                \XLite\Core\Config::getInstance()->XC->TwoFactorAuthentication->api_key
                && $profile->getAuthPhoneNumber()
                && $profile->getAuthPhoneCode()
                && (\XLite\Core\Config::getInstance()->XC->TwoFactorAuthentication->customer_interface || $isAdmin)
            ) {
                \XLite\Core\Session::getInstance()->preauth_authy_profile_id = $profile->getProfileId();

                $isUses = true;
            }
        }

        return $isUses;
    }

    /**
     * Alternative login body
     *
     * @return void
     */
    protected function alternativeLoginBody()
    {
        $returnURL = $this->buildURL(
            'authy_login',
            '',
            array(
                'widget'    => '\XLite\Module\XC\TwoFactorAuthentication\View\CustomerLogin',
                'preReturnURL' => \XLite\Core\Request::getInstance()->returnURL,
                )
            );
        $this->setReturnURL($returnURL);

        $this->setHardRedirect(false);
        $this->setInternalRedirect();
    }
}