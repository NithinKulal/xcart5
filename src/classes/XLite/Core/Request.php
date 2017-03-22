<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core;

/**
 * Request
 */
class Request extends \XLite\Base\Singleton
{
    /**
     * Current method
     */
    const METHOD_CLI = 'cli';

    /**
     * Current request method
     *
     * @var string
     */
    protected $requestMethod;

    /**
     * Request data (filtered)
     *
     * @var array
     */
    protected $data = array();

    /**
     * Request data (non-filtered)
     *
     * @var array
     */
    protected $nonFilteredData = array();

    /**
     * Request raw data (parsed manually from php://input)
     *
     * @var array
     */
    protected $rawData = array();

    /**
     * Cache for mobile device flag
     *
     * @var null|boolean
     */
    protected static $isMobileDeviceFlag;

    /**
     * current language ISO 639-1 code
     *
     * @var string
     */
    protected $languageCode = '';

    /**
     * @return string
     */
    public function getLanguageCode()
    {
        return $this->languageCode;
    }
    
    /**
     * @param $languageCode
     *
     * @return $this
     */
    public function setLanguageCode($languageCode)
    {
        $this->languageCode = $languageCode;

        return $this;
    }

    /**
     * Drag-n-drop-cart feature is turned off for the mobile devices
     *
     * @return boolean
     */
    public static function isDragDropCartFlag()
    {
        return !static::isMobileDevice();
    }

    /**
     * Detect Mobile version
     *
     * @return boolean
     */
    public static function isMobileDevice()
    {
        if (null === static::$isMobileDeviceFlag) {
            static::$isMobileDeviceFlag = static::detectMobileDevice();
        }

        return static::$isMobileDeviceFlag;
    }

    /**
     * Defines if the device is a tablet
     *
     * @return boolean
     */
    public static function isTablet()
    {
        return \XLite\Core\MobileDetect::getInstance()->isTablet();
    }

    /**
     * Detect if browser is mobile device
     *
     * @return boolean
     */
    public static function detectMobileDevice()
    {
        return \XLite\Core\MobileDetect::getInstance()->isMobile();
    }

    /**
     * Map request data
     *
     * @param array $data Custom data OPTIONAL
     *
     * @return void
     */
    public function mapRequest(array $data = array())
    {
        if (empty($data)) {
            if ($this->isCLI()) {
                for ($i = 1; count($_SERVER['argv']) > $i; $i++) {
                    $pair = explode('=', $_SERVER['argv'][$i], 2);
                    $data[preg_replace('/^-+/S', '', $pair[0])] = isset($pair[1]) ? trim($pair[1]) : true;
                }

            } else {
                $data = array_merge($this->getCookieData(false), $this->getGetData(false), $this->getPostData(false));
            }
        }

        $this->nonFilteredData = array_replace_recursive($this->data, $this->prepare($data));

        $this->data = $this->filterData($this->nonFilteredData);
    }

    /**
     * Return all data
     *
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Return all non-filtered data
     *
     * @return array
     */
    public function getNonFilteredData()
    {
        return $this->nonFilteredData;
    }

    /**
     * Get manually parsed post data
     */
    public function getRawPostData()
    {
        if (empty($this->rawData)) {
            $this->rawData = array();
            $raw_post_array = explode('&', file_get_contents('php://input'));
            foreach ($raw_post_array as $keyval) {
                $keyval = explode ('=', $keyval);
                if (count($keyval) == 2) {
                    $name = urldecode($keyval[0]);
                    $value = urldecode($keyval[1]);
                    $this->rawData[$name] = $value;
                }
            }
        }
        return $this->rawData;
    }

    /**
     * Return parsed request body with handy format
     * This is needed because of names like 'transaction[0].status' which will not be parsed
     * by standart technics
     *
     * @return array
     */
    public function getPostDataWithArrayValues()
    {
        $rawData = $this->getRawPostData();
        $output  = array();
        $pattern = '/(?>([\w]*)\[(\d)\]\.)(\w*?)$/';

        foreach ($rawData as $name => $value) {
            preg_match($pattern, $name, $matches);

            if (count($matches) > 3) {
                list($full, $name, $index, $subname) = $matches;
                $output[$name][$index][$subname] = $value;

            } else {
                $output[$name] = $value;
            }
        }

        return $output;
    }

    /**
     * Return data from the $_GET global variable
     *
     * @param boolean $prepare Flag OPTIONAL
     *
     * @return array
     */
    public function getGetData($prepare = true)
    {
        return $prepare ? $this->prepare($_GET) : (array)$_GET;
    }

    /**
     * Return data from the $_POST global variable
     *
     * @param boolean $prepare Flag OPTIONAL
     *
     * @return array
     */
    public function getPostData($prepare = true)
    {
        return $prepare ? $this->prepare($_POST) : (array)$_POST;
    }

    /**
     * Return data from the $_COOKIE global variable
     *
     * @param boolean $prepare Flag OPTIONAL
     *
     * @return array
     */
    public function getCookieData($prepare = true)
    {
        return $prepare ? $this->prepare($_COOKIE) : (array)$_COOKIE;
    }

    /**
     * Return data from the $_SERVER global variable
     *
     * @param boolean $prepare Flag OPTIONAL
     *
     * @return array
     */
    public function getServerData($prepare = true)
    {
        return $prepare ? $this->prepare($_SERVER) : (array)$_SERVER;
    }

    /**
     * Return current request method
     *
     * @return string
     */
    public function getRequestMethod()
    {
        return $this->requestMethod;
    }

    /**
     * Set request method
     *
     * @param string $method New request method
     *
     * @return void
     */
    public function setRequestMethod($method)
    {
        $this->requestMethod = $method;
    }

    /**
     * Check if current request method is "GET"
     *
     * @return boolean
     */
    public function isGet()
    {
        return 'GET' === $this->requestMethod;
    }

    /**
     * Check if current request method is "POST"
     *
     * @return boolean
     */
    public function isPost()
    {
        return 'POST' === $this->requestMethod;
    }

    /**
     * Check if current request method is "HEAD"
     *
     * @return boolean
     */
    public function isHead()
    {
        return 'HEAD' === $this->requestMethod;
    }

    /**
     * Check - is AJAX request or not
     *
     * @return boolean
     */
    public function isAJAX()
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';
    }

    /**
     * Check for secure connection
     *
     * @return boolean
     */
    public function isHTTPS()
    {
        return \XLite\Core\URLManager::isHTTPS();
    }

    /**
     * Check - is command line interface or not
     *
     * @return boolean
     */
    public function isCLI()
    {
        return 'cli' === PHP_SAPI;
    }

    /**
     * Getter
     *
     * @param string $name Property name
     *
     * @return mixed
     */
    public function __get($name)
    {
        return isset($this->data[$name]) ? $this->data[$name] : null;
    }

    /**
     * Setter
     *
     * @param string $name  Property name
     * @param mixed  $value Property value
     *
     * @return void
     */
    public function __set($name, $value)
    {
        $this->data[$name] = $this->prepare($value);
    }

    /**
     * Check property accessability
     *
     * @param string $name Property name
     *
     * @return boolean
     */
    public function __isset($name)
    {
        return isset($this->data[$name]);
    }

    /**
     * Un-setter
     *
     * @param string $name Property name
     *
     * @return void
     */
    public function __unset($name)
    {
        unset($this->data[$name]);
    }

    /**
     * Constructor
     */
    protected function __construct()
    {
        $this->requestMethod = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : self::METHOD_CLI;
        $this->mapRequest();
    }

    /**
     * Unescape single value
     *
     * @param string $value Value to sanitize
     *
     * @return string
     */
    protected function doUnescapeSingle($value)
    {
        return stripslashes($value);
    }

    /**
     * Remove automatically added escaping
     *
     * @param mixed $data Data to sanitize
     *
     * @return mixed
     */
    protected function doUnescape($data)
    {
        return is_array($data)
            ? array_map(array($this, __FUNCTION__), $data)
            : $this->doUnescapeSingle($data);
    }

    /**
     * Normalize request data
     *
     * @param mixed $request Request data
     *
     * @return mixed
     */
    protected function normalizeRequestData($request)
    {
        if (ini_get('magic_quotes_gpc')) {
            $request = $this->doUnescape($request);
        }

        return $request;
    }

    /**
     * Wrapper for sanitize()
     *
     * @param mixed $data Data to sanitize
     *
     * @return mixed
     */
    protected function prepare($data)
    {
        if (is_array($data)) {
            if (isset($data['target']) && !$this->checkControlArgument($data['target'], 'Target')) {
                $data['target'] = \XLite::TARGET_404;
                $data['action'] = null;
            }

            if (isset($data['action']) && !$this->checkControlArgument($data['action'], 'Action')) {
                unset($data['action']);
            }
        }

        return $this->normalizeRequestData($data);
    }

    /**
     * Filter data
     *
     * @param array $data Array of non-filtered data
     *
     * @return array
     */
    protected function filterData($data)
    {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                if (is_array($value)) {
                    $value = $this->filterData($value);

                } elseif (!empty($value) && !is_numeric($value)) {
                    $value = strip_tags($value);
                }

                $data[$key] = $value;
            }
        }

        return $data;
    }

    /**
     * Check control argument (like target)
     *
     * @param mixed  $value Argument value
     * @param string $name  Argument name
     *
     * @return boolean
     */
    protected function checkControlArgument($value, $name)
    {
        $result = true;

        if (!is_string($value)) {
            \XLite\Logger::getInstance()->logPostponed($name . ' has a wrong type');
            $result = false;

        } elseif (!preg_match('/^[a-z0-9_]*$/Si', $value)) {
            \XLite\Logger::getInstance()->logPostponed($name . ' has a wrong format');
            $result = false;
        }

        return $result;
    }

    // {{{ Cookie

    /**
     * Set cookie
     *
     * @param string  $name  Name
     * @param string  $value Value
     * @param integer $ttl   TTL OPTIONAL
     *
     * @return boolean
     */
    public function setCookie($name, $value, $ttl = 0)
    {
        $result = true;

        $ttl = $ttl != 0 ? \XLite\Core\Converter::time() + $ttl : 0;

        $httpDomain = $this->getCookieDomain(false);
        $result = @setcookie(
            $name,
            $value,
            $ttl,
            $this->getCookiePath(false),
            $httpDomain,
            false,
            true
        ) && $result;

        $httpsDomain = $this->getCookieDomain(true);
        if ($httpDomain != $httpsDomain) {
            $result = @setcookie(
                $name,
                $value,
                $ttl,
                $this->getCookiePath(true),
                $httpsDomain,
                true,
                true
            ) && $result;
        }

        return $result;
    }

    public function unsetCookie($name)
    {
        if (isset($_COOKIE[$name])) {
            unset($_COOKIE[$name]);
        }

        $httpDomain = $this->getCookieDomain(false);
        @setcookie(
            $name,
            null,
            -1,
            $this->getCookiePath(false),
            $httpDomain,
            false,
            true
        );

        $httpsDomain = $this->getCookieDomain(true);
        if ($httpDomain != $httpsDomain) {
            @setcookie(
                $name,
                null,
                -1,
                $this->getCookiePath(true),
                $httpsDomain,
                true,
                true
            );
        }
    }

    /**
     * Get host / domain for Set-Cookie
     *
     * @param boolean $secure Secure protocol or not OPTIONAL
     *
     * @return string
     */
    protected function getCookieDomain($secure = false)
    {
        $url = $this->getCookieURL($secure);

        return false === strpos($url['host'], '.') ? false : $url['host'];
    }

    /**
     * Get URL path for Set-Cookie
     *
     * @param boolean $secure Secure protocol or not OPTIONAL
     *
     * @return string
     */
    protected function getCookiePath($secure = false)
    {
        $url = $this->getCookieURL($secure);

        return isset($url['path']) ? $url['path'] : '/';
        return !empty($url['path']) ? $url['path'] : '/';
    }

    /**
     * Get parsed URL for Set-Cookie
     *
     * @param boolean $secure Secure protocol or not OPTIONAL
     *
     * @return array
     */
    protected function getCookieURL($secure = false)
    {
        $url = $secure
            ? 'https://' .  \XLite::getInstance()->getOptions(array('host_details', 'https_host'))
            : 'http://' . \XLite::getInstance()->getOptions(array('host_details', 'http_host'));

        $url .= \XLite::getInstance()->getOptions(array('host_details', 'web_dir'));

        return parse_url($url);
    }

    // }}}

    // {{{ Bot detected

    /**
     * Cookie cell for robot mark
     */
    const COOKIE_ROBOT = 'is_robot';

    /**
     * Bot flag
     *
     * @var   boolean
     */
    protected $botFlag;

    /**
     * Bot signatures
     *
     * @var array
     */
    protected $botSignatures = array (
        'X-Cart info'    => array(
            'X-Cart info',
            'X-Cart Catalog Generator',
        ),
        'Google'        => array(
            'Googlebot',
            'Mediapartners-Google',
            'Googlebot-Mobile',
            'Googlebot-Image',
            'Adsbot-Google',
        ),
        'Yahoo'            => array(
            'Slurp',
            'YahooSeeker',
        ),
        'Microsoft'        => array(
            'MSNBot',
            'MSNBot-Media',
            'MSNBot-NewsBlogs',
            'MSNBot-Products',
            'MSNBot-Academic',
            'bingbot',
            'adidxbot',
        ),
        'Ask'            => array(
            'Teoma',
        ),
        'Baidu'          => array(
            'baiduspider',
        ),
        'dotnetdotcom.org' => array(
            'dotbot',
        ),
        'Qihoo' => array(
            'Qihoo',
        ),
        'Tencent' => array(
            'Sosospider',
        ),
        'Entireweb.com' => array(
            'Speedy Spider',
        ),
        'WuKong' => array(
            'WukongSpider',
        ),
        'NHN Corporation' => array(
            'Yeti',
        ),
        'NetEase Inc' => array(
           'YodaoBot',
        ),
        'MJ12bot' => array(
           'MJ12bot',
        ),
        'AhrefsBot' => array(
           'AhrefsBot',
        ),
    );

    /**
     * Check - current user is bot or not
     *
     * @return boolean
     */
    public function isBot()
    {
        if (null === $this->botFlag) {
            $this->botFlag = false;

            if (!$this->isCLI() && !empty($_SERVER['HTTP_USER_AGENT'])) {
                if (isset($_COOKIE[static::COOKIE_ROBOT])) {
                    if (substr($_COOKIE[static::COOKIE_ROBOT], 0, -1) == hash('md4', $_SERVER['HTTP_USER_AGENT'])) {
                        $this->botFlag = (bool)substr($_COOKIE[static::COOKIE_ROBOT], -1);

                    } else {
                        $this->unsetCookie(static::COOKIE_ROBOT);
                    }

                } else {
                    $this->botFlag = $this->detectBot();
                    if ($this->botFlag) {
                        $this->setCookie(static::COOKIE_ROBOT, hash('md4', $_SERVER['HTTP_USER_AGENT']) . ((int) $this->botFlag));
                    }
                }
            }
        }

        return $this->botFlag;
    }

    /**
     * Detect bot
     *
     * @return boolean
     */
    protected function detectBot()
    {
        $result = false;

        foreach ($this->botSignatures as $name => $signatures) {
            foreach ($signatures as $signature) {
                if (false !== stristr($_SERVER['HTTP_USER_AGENT'], $signature)) {
                    $result = true;
                    break;
                }
            }
        }

        return $result;
    }

    // }}}

    // {{{ Browser detection

    /**
     * Browscap cache
     *
     * @var \stdClass|array
     */
    protected $browscap;

    /**
     * Get browser data
     *
     * @param boolean $override Override cache OPTIONAL
     *
     * @return \stdClass|array
     */
    public function getBrowserData($override = false)
    {
        if ((null === $this->browscap || $override)
            && !$this->isCLI()
            && !empty($_SERVER['HTTP_USER_AGENT'])
        ) {
            $this->browscap = $this->getBrowscapClient()->getBrowser($_SERVER['HTTP_USER_AGENT']);
        }

        return $this->browscap;
    }

    /**
     * Get browscap client
     *
     * @return \phpbrowscap\Browscap
     */
    protected function getBrowscapClient()
    {
        require_once (LC_DIR_LIB . 'Browscap.php');

        $cacheDir = LC_DIR_DATA . 'browscap';
        if (!file_exists($cacheDir)) {
            \Includes\Utils\FileManager::mkdirRecursive($cacheDir);
            \Includes\Utils\FileManager::copy(
                $this->getBrowsCapPath(),
                $cacheDir . LC_DS . 'browscap.ini'
            );
        }

        return new \phpbrowscap\Browscap($cacheDir);
    }

    /**
     * Get browsCap file path
     *
     * @return string
     */
    protected function getBrowsCapPath()
    {
        return LC_DIR_LIB . 'browscap.ini';
    }

    // }}}

    /**
     * Returns customer ip
     *
     * @return string
     */
    public function getClientIp()
    {
        if (isset($_SERVER['REMOTE_ADDR'])) {
            $ipAddress = $_SERVER['REMOTE_ADDR'];
        } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ipAddress = $_SERVER['HTTP_CLIENT_IP'];
        } else if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ipAddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else if (isset($_SERVER['HTTP_X_FORWARDED'])) {
            $ipAddress = $_SERVER['HTTP_X_FORWARDED'];
        } else if (isset($_SERVER['HTTP_FORWARDED_FOR'])) {
            $ipAddress = $_SERVER['HTTP_FORWARDED_FOR'];
        } else if (isset($_SERVER['HTTP_FORWARDED'])) {
            $ipAddress = $_SERVER['HTTP_FORWARDED'];
        } else {
            $ipAddress = 'UNKNOWN';
        }

        return $ipAddress;
    }
}
