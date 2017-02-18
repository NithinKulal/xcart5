<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Includes\Utils;

/**
 * URLManager
 *
 */
abstract class URLManager extends \Includes\Utils\AUtils
{
    /**
     * URL output type codes
     */
    const URL_OUTPUT_SHORT = 'short';
    const URL_OUTPUT_FULL  = 'full';

    /**
     * @var https flag
     */
    protected static $isHTTPS;

    /**
     * @param      $url
     * @param null $time
     *
     * @return string
     */
    public static function addTimestampToUrl($url, $time = null)
    {
        $time = $time ?: time();

        $query = parse_url($url, PHP_URL_QUERY);

        return $query
            ? $url . '&t=' . $time
            : $url . '?t=' . $time;
    }

    /**
     * Remove trailing slashes from URL
     *
     * @param string $url URL to prepare
     *
     * @return string
     */
    public static function trimTrailingSlashes($url)
    {
        return \Includes\Utils\Converter::trimTrailingChars($url, '/');
    }

    /**
     * Return full URL for the resource
     *
     * @param string  $url             URL part to add           OPTIONAL
     * @param boolean $isSecure        Use HTTP or HTTPS         OPTIONAL
     * @param array   $params          URL parameters            OPTIONAL
     * @param string  $output          URL output type           OPTIONAL
     * @param boolean $isSession       Use session ID parameter  OPTIONAL
     * @param boolean $isProtoRelative Use protocol-relative URL OPTIONAL
     *
     * @return string
     */
    public static function getShopURL(
        $url = '',
        $isSecure = null,
        array $params = array(),
        $output = null,
        $isSession = null,
        $isProtoRelative = false
    ) {
        $url = trim($url);
        if (!preg_match('/^https?:\/\//Ss', $url)) {

            // We are using the protocol-relative URLs for resources
            $protocol = (true === $isSecure || (is_null($isSecure) && static::isHTTPS())) ? 'https' : 'http';

            if (!isset($output)) {
                $output = static::URL_OUTPUT_FULL;
            }

            $hostDetails = static::getOptions('host_details');
            $host = $hostDetails[$protocol . '_host'];

            if (!$host && !\Includes\Utils\ConfigParser::getOptions(array('database_details', 'database'))) {
                $phpSelf = rtrim(dirname($_SERVER["PHP_SELF"]), '/');
                $host = $_SERVER['HTTP_HOST'] . $phpSelf;
            }

            if ($host) {
                if ('/' != substr($url, 0, 1)) {
                    $url = $hostDetails['web_dir_wo_slash'] . '/' . $url;
                }

                $isSession = !isset($isSession)
                    ? (true === $isSecure && !static::isHTTPS())
                    : $isSession;

                if ($isSession) {
                    $session = \XLite\Core\Session::getInstance();
                    $url .= (false !== strpos($url, '?') ? '&' : '?') . $session->getName() . '=' . $session->getID();
                }

                foreach ($params as $name => $value) {
                    $url .= (false !== strpos($url, '?') ? '&' : '?') . $name . '=' . $value;
                }

                if (static::URL_OUTPUT_FULL == $output) {
                    if (substr($url, 0, 2) != '//') {
                        $url = '//' . $host . $url;
                    }

                    $url = ($isProtoRelative ? '' : ($protocol . ':')) . $url;
                }
            }
        }

        return $url;
    }

    /**
     * Return protocol-relative URL for the resource
     *
     * @param string  $url    URL part to add OPTIONAL
     * @param array   $params URL parameters            OPTIONAL
     * @param string  $output URL output type           OPTIONAL
     *
     * @return string
     */
    public static function getProtoRelativeShopURL(
        $url = '',
        array $params = array(),
        $output = null
    ) {
        if (!preg_match('/^https?:\/\//Ss', $url)) {
            if (!isset($output)) {
                $output = static::URL_OUTPUT_FULL;
            }
            $hostDetails = \Includes\Utils\ConfigParser::getOptions('host_details');
            $host        = $hostDetails[static::isHTTPS() ? 'https_host' : 'http_host'];
            if ($host) {
                if ('/' != substr($url, 0, 1)) {
                    $url = $hostDetails['web_dir_wo_slash'] . '/' . $url;
                }

                foreach ($params as $name => $value) {
                    $url .= (false !== strpos($url, '?') ? '&' : '?') . $name . '=' . $value;
                }

                if (static::URL_OUTPUT_FULL == $output) {
                    // We are using the protocol-relative URLs for resources
                    $url = '//' . $host . $url;
                }
            }
        }

        return $url;
    }

    /**
     * Check for secure connection
     *
     * @return boolean
     */
    public static function isHTTPS()
    {
        if (null === static::$isHTTPS) {
            static::$isHTTPS = (isset($_SERVER['HTTPS']) && ('on' === strtolower($_SERVER['HTTPS']) || '1' == $_SERVER['HTTPS']))
                || (isset($_SERVER['SERVER_PORT']) && '443' == $_SERVER['SERVER_PORT'])
                || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && 'https' == $_SERVER['HTTP_X_FORWARDED_PROTO']);
        }

        return static::$isHTTPS;
    }

    /**
     * Return current URI
     *
     * @return string
     */
    public static function getSelfURI()
    {
        return isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : null;
    }

    /**
     * Return current URL
     *
     * @return string
     */
    public static function getCurrentURL()
    {
        return 'http' . (static::isHTTPS() ? 's' : '') . '://' . $_SERVER['HTTP_HOST']
        . (isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '');
    }

    /**
     * Return current shop URL
     *
     * @return string
     */
    public static function getCurrentShopURL()
    {
        $host = 'http' . (static::isHTTPS() ? 's' : '') . '://' . $_SERVER['HTTP_HOST'];
        $webdir = static::getWebdir() ? '/' . static::getWebdir() : '';
        return $host . $webdir;
    }   

    /**
     * Returns webdir.
     * 
     * @return string
     */
    public static function getWebdir()
    {
        $hostDetails = static::getOptions('host_details');
        return $hostDetails['web_dir'];
    }

    /**
     * Check if provided string is a valid host part of URL
     *
     * @param string $str Host string
     *
     * @return boolean
     */
    public static function isValidURLHost($str)
    {
        $urlData = parse_url('http://' . $str . '/path');
        $host = $urlData['host'] . (isset($urlData['port']) ? ':' . $urlData['port'] : '');

        return ($host == $str);
    }

    /**
     * Get list of available shop domains
     *
     * @return array
     */
    public static function getShopDomains()
    {
        $result = array();

        $hostDetails = \Includes\Utils\ConfigParser::getOptions(array('host_details'));
        $result[] = !empty($hostDetails['http_host_orig']) ? $hostDetails['http_host_orig'] : $hostDetails['http_host'];
        $result[] = !empty($hostDetails['https_host_orig']) ? $hostDetails['https_host_orig'] : $hostDetails['https_host'];

        $domains = explode(',', $hostDetails['domains']);

        if (!empty($domains) && is_array($domains)) {
            foreach ($domains as $domain) {
                $result[] = $domain;
            }
        }

        return array_unique($result);
    }

    /**
     * Get options
     *
     * @param mixed $option Option
     *
     * @return mixed
     */
    protected static function getOptions($option)
    {
        return \Includes\Utils\ConfigParser::getOptions($option);
    }
}
