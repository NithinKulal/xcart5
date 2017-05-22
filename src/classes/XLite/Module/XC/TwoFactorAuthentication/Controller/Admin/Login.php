<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\TwoFactorAuthentication\Controller\Admin;

/**
 * Login
 */
abstract class Login extends \XLite\Controller\Admin\Login implements \XLite\Base\IDecorator
{
    /**
     * Login
     *
     * @return void
     */
    protected function doActionLogin()
    {
        parent::doActionLogin();

        if (
            \XLite\Core\Auth::getInstance()->isLogged()
            && \XLite\Core\Config::getInstance()->XC->TwoFactorAuthentication->admin_interface
            && \XLite\Core\Config::getInstance()->XC->TwoFactorAuthentication->api_key
            && \XLite\Core\Auth::getInstance()->getProfile()->getAuthPhoneNumber()
        ) {
            \XLite\Core\Session::getInstance()->preauth_authy_profile_id = \XLite\Core\Auth::getInstance()
                ->getProfile()
                ->getProfileId();
            \XLite\Core\Auth::getInstance()->logOff();

            $returnURL = $this->buildURL('authy_login');
            $this->setReturnURL($returnURL);
        }
    }
}