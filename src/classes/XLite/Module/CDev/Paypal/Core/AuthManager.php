<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\Core;

/**
 * Maintains the list of auth providers
 */
class AuthManager extends \XLite\Module\CDev\SocialLogin\Core\AuthManager implements \XLite\Base\IDecorator
{
    /**
     * Get all available authentication providers class names
     *
     * @return array List of auth provider class names (\XLite\Module\CDev\SocialLogin\Core\AAuthProvider descendants)
     */
    protected static function getAuthProvidersClassNames()
    {
        $list = parent::getAuthProvidersClassNames();
        $list[] = 'XLite\Module\CDev\Paypal\Core\PaypalAuthProvider';

        return $list;
    }
}
