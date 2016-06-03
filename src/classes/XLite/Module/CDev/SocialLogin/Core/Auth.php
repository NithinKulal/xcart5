<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\SocialLogin\Core;

/**
 * Authorization routine
 */
class Auth extends \XLite\Core\Auth implements \XLite\Base\IDecorator
{
    /**
     * Logs in user to cart
     *
     * @param string $login      User's login
     * @param string $password   User's password
     * @param string $secureHash Secret token OPTIONAL
     *
     * @return \XLite\Model\Profile|integer
     */
    public function login($login, $password, $secureHash = null)
    {
        $profile = \XLite\Core\Database::getRepo('XLite\Model\Profile')->findOneBy(
            array('login' => $login, 'order' => null)
        );

        if ($profile && $profile->isSocialProfile()) {
            $result = static::RESULT_ACCESS_DENIED;
        }

        return isset($result) ? $result : parent::login($login, $password, $secureHash);
    }
}
