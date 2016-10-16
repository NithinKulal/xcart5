<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CrispWhiteSkin\View;

/**
 * Signin
 *
 * @Decorator\Depend("CDev\SocialLogin")
 * @Decorator\After("XC\CrispWhiteSkin")
 */
abstract class SigninSocialLogin extends \XLite\View\Signin implements \XLite\Base\IDecorator
{
    protected function getWrapperStyleClass()
    {
        $result = parent::getWrapperStyleClass();

        if (\XLite\Module\CDev\SocialLogin\Core\AuthManager::getAuthProviders()) {
            $result .= ' social-login';
        }

        return $result;
    }
}
