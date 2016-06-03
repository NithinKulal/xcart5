<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\SocialLogin\Core;

/**
 * Maintains the list of auth providers
 */
class AuthManager extends \XLite\Base
{
    /**
     * Get all available authentication providers instances
     *
     * @return array List of auth provider objects (\XLite\Module\CDev\SocialLogin\Core\AAuthProvider descendants)
     */
    public static function getAuthProviders()
    {
        return array_filter(
            array_map(
                function ($className) {
                    return $className::getInstance();
                },
                static::getAuthProvidersClassNames()
            ),
            function ($provider) {
                return $provider->isConfigured();
            }
        );
    }

    /**
     * Get all available authentication providers class names
     *
     * @return array List of auth provider class names (\XLite\Module\CDev\SocialLogin\Core\AAuthProvider descendants)
     */
    protected static function getAuthProvidersClassNames()
    {
        return array(
            '\XLite\Module\CDev\SocialLogin\Core\FacebookAuthProvider',
            '\XLite\Module\CDev\SocialLogin\Core\GoogleAuthProvider',
        );
    }
}
