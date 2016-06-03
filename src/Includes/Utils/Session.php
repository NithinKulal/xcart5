<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Includes\Utils;

/**
 * Current session
 */
class Session extends \Includes\Utils\AUtils
{
    /**
     * Admin cookie name
     */
    const ADMIN_COOKIE_NAME = 'xid_admin_logged';

    /**
     * Admin cookie value
     */
    const ADMIN_COOKIE_VALUE = 'admin_logged';

    /**
     * Set the admin cookie with the defined value
     *
     * @return void
     */
    public static function setAdminCookie()
    {
        static::setCookieWrapper(static::getAdminCookieName(), static::getAdminCookieValue());
    }

    /**
     * Clear the admin cookie
     *
     * @return void
     */
    public static function clearAdminCookie()
    {
        static::setCookieWrapper(static::getAdminCookieName(), false);
    }

    /**
     * Check if the admin cookie is set
     *
     * @return boolean
     */
    public static function issetAdminCookie()
    {
        return isset($_COOKIE[static::getAdminCookieName()])
            ? (static::getAdminCookieValue() === $_COOKIE[static::getAdminCookieName()])
            : false;
    }

    /**
     * Defines the admin cookie name
     *
     * @return string
     */
    protected static function getAdminCookieName()
    {
        return static::ADMIN_COOKIE_NAME;
    }

    /**
     * Defines the admin cookie value
     *
     * @return string
     */
    protected static function getAdminCookieValue()
    {
        return static::ADMIN_COOKIE_VALUE;
    }

    /**
     * Set cookie
     *
     * @param string $name  Name of cookie variable
     * @param string $value Value of cookie variable
     *
     * @return void
     */
    protected static function setCookieWrapper($name, $value)
    {
        if (
            !headers_sent()
            && 'cli' != PHP_SAPI
        ) {
            $httpDomain = static::getCookieDomain();
            $httpsDomain = static::getCookieDomain(true);

            setcookie(
                $name,
                $value,
                0,
                static::getCookiePath(),
                $httpDomain,
                false,
                true
            );

            if ($httpDomain != $httpsDomain) {
                setcookie(
                    $name,
                    $value,
                    0,
                    static::getCookiePath(true),
                    $httpsDomain,
                    false,
                    true
                );
            }
        }
    }

    /**
     * Get parsed URL for Set-Cookie
     *
     * @param boolean $secure Secure protocol or not OPTIONAL
     *
     * @return array
     */
    protected static function getCookieURL($secure = false)
    {
        $url = $secure
            ? 'http://' .  \Includes\Utils\ConfigParser::getOptions(array('host_details', 'http_host'))
            : 'https://' . \Includes\Utils\ConfigParser::getOptions(array('host_details', 'https_host'));

        $url .= \Includes\Utils\ConfigParser::getOptions(array('host_details', 'web_dir'));

        return parse_url($url);
    }

    /**
     * Get host / domain for Set-Cookie
     *
     * @param boolean $secure Secure protocol or not OPTIONAL
     *
     * @return string
     */
    protected static function getCookieDomain($secure = false)
    {
        $url = static::getCookieURL($secure);

        return false === strstr($url['host'], '.') ? false : $url['host'];
    }

    /**
     * Get URL path for Set-Cookie
     *
     * @param boolean $secure Secure protocol or not OPTIONAL
     *
     * @return string
     */
    protected static function getCookiePath($secure = false)
    {
        $url = static::getCookieURL($secure);

        return isset($url['path']) ? $url['path'] : '/';
    }
}
