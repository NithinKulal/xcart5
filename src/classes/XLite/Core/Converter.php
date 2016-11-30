<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core;

use Includes\ClassPathResolver;
use Includes\Reflection\StaticReflectorFactory;

/**
 * Miscellaneous conversion routines
 */
class Converter extends \XLite\Base\Singleton
{
    /**
     * Sizes
     */
    const GIGABYTE = 1073741824;
    const MEGABYTE = 1048576;
    const KILOBYTE = 1024;

    /**
     * Cache driver
     *
     * @var \XLite\Core\Cache\Registry
     */
    protected static $cacheDriver;

    /**
     * Cache controllers
     *
     * @var array
     */
    protected static $cacheControllers;

    /**
     * Method name translation records
     *
     * @var array
     */
    protected static $to = array(
        'Q', 'W', 'E', 'R', 'T',
        'Y', 'U', 'I', 'O', 'P',
        'A', 'S', 'D', 'F', 'G',
        'H', 'J', 'K', 'L', 'Z',
        'X', 'C', 'V', 'B', 'N',
        'M',
    );

    /**
     * Method name translation patterns
     *
     * @var array
     */
    protected static $from = array(
        '_q', '_w', '_e', '_r', '_t',
        '_y', '_u', '_i', '_o', '_p',
        '_a', '_s', '_d', '_f', '_g',
        '_h', '_j', '_k', '_l', '_z',
        '_x', '_c', '_v', '_b', '_n',
        '_m',
    );

    /**
     * Flag to avoid multiple setlocale() calls
     *
     * @var boolean
     */
    protected static $isLocaleSet = false;

    /**
     * Charsets table
     *
     * @var array
     */
    protected static $charsetsTable = array(
        'ru' => 'WINDOWS-1251',
    );

    /**
     * Run-time cached list of locales
     * array ( <lng_code> => <locale>, ...)
     *
     * @var array
     */
    protected static $localesCache;

    /**
     * Convert a string like "test_foo_bar" into the camel case (like "TestFooBar")
     *
     * @param string $string String to convert
     *
     * @return string
     */
    public static function convertToCamelCase($string)
    {
        return ucfirst(str_replace(self::$from, self::$to, (string) $string));
    }

    /**
     * Convert a string like "testFooBar" into the underline style (like "test_foo_bar")
     *
     * @param string $string String to convert
     *
     * @return string
     */
    public static function convertFromCamelCase($string)
    {
        return str_replace(self::$to, self::$from, lcfirst((string) $string));
    }

    /*
     *  Convert a string like "testFooBar" to translit
     *
     * @param string $string String to convert
     *
     * @return string
     */
    public static function convertToTranslit($string)
    {
        return \Includes\Utils\Converter::convertToTranslit($string);
    }

    /**
     * Prepare method name
     *
     * @param string $string Underline-style string
     *
     * @return string
     */
    public static function prepareMethodName($string)
    {
        return str_replace(self::$from, self::$to, (string) $string);
    }

    /**
     * Compose controller class name using target
     *
     * @param string $target Current target
     *
     * @return string
     */
    public static function getControllerClass($target)
    {
        $zone = 'Customer';

        if (\XLite\Core\Request::getInstance()->isCLI()) {
            $zone = 'Console';

        } elseif (\XLite::isAdminZone()) {
            $zone = 'Admin';
        }

        $target = static::convertToCamelCase($target);

        // Initialize cache
        if (null === static::$cacheControllers) {
            static::$cacheControllers = static::getSystemCacheDriver()->fetch('controllers');
            if (!is_array(static::$cacheControllers)) {
                static::$cacheControllers = array();
            }
        }

        // Check cache
        $class = isset(static::$cacheControllers[$zone . '.' . $target])
            ? static::$cacheControllers[$zone . '.' . $target]
            : null;

        if (!$class
            || !\XLite\Core\Operator::isClassExists($class)
            || !file_exists(LC_DIR_CLASSES . str_replace('\\', LC_DS, $class) . '.php')
        ) {
            // Detect
            $prefix = 'Controller\\' . $zone . '\\' . static::convertToCamelCase($target);
            $class = 'XLite\\' . $prefix;

            // If common controller
            if (!\XLite\Core\Operator::isClassExistsInClassesOrCache($class)) {
                $prefix = 'Controller\\' . static::convertToCamelCase($target);
                $class = 'XLite\\' . $prefix;
            }

            // If non core controller
            if (!\XLite\Core\Operator::isClassExistsInClassesOrCache($class)) {
                $class = null;
                $base = LC_DEVELOPER_MODE
                    ? LC_DIR_CLASSES
                    : LC_DIR_CACHE_CLASSES;
                $path = $base . 'XLite' . LC_DS
                    . 'Module' . LC_DS
                    . '*' . LC_DS
                    . '*' . LC_DS
                    . 'Controller' . LC_DS
                    . $zone . LC_DS
                    . $target . '.php';

                $list = glob($path);
                if ($list) {
                    $length = strlen($base);
                    foreach ($list as $path) {
                        $preclass = str_replace(LC_DS, '\\', substr($path, $length, -4));
                        if (!LC_DEVELOPER_MODE
                            || \XLite\Core\Operator::isClassExists($preclass)
                            || class_exists($preclass, LC_DEVELOPER_MODE)
                        ) {
                            $reflection = new \ReflectionClass($preclass);
                            // Check - decorator or not
                            if (!$reflection->isAbstract()) {
                                $class = $preclass;
                                break;
                            }

                        } elseif (\XLite\Core\Operator::isClassExistsInClassesOrCache($preclass)) {
                            $sourceStaticReflectorFactory = new StaticReflectorFactory(
                                new ClassPathResolver(LC_DIR_CLASSES)
                            );

                            if (!$sourceStaticReflectorFactory->reflectClass($preclass)->isDecorator()) {
                                $class = $preclass;
                                break;
                            }
                        }
                    }
                }
            }

            if ($class) {
                static::$cacheControllers[$zone . '.' . $target] = $class;
                static::getSystemCacheDriver()->save('controllers', static::$cacheControllers);
            }
        }

        if (LC_DEVELOPER_MODE
            && isset(static::$cacheControllers[$zone . '.' . $target])
            && !file_exists(LC_DIR_CLASSES . str_replace('\\', LC_DS, $class) . '.php')
        ) {
            unset(static::$cacheControllers[$zone . '.' . $target]);
            static::getSystemCacheDriver()->save('controllers', static::$cacheControllers);
            $class = null;
        }

        return $class;
    }

    /**
     * Get cache driver
     *
     * @return \XLite\Core\Cache\Registry
     */
    public static function getSystemCacheDriver()
    {
        if (null === static::$cacheDriver) {
            static::$cacheDriver = new \XLite\Core\Cache\Registry('system');
        }

        return static::$cacheDriver;
    }

    // {{{ URL routines

    /**
     * Compose URL from target, action and additional params
     *
     * @param string  $target        Page identifier OPTIONAL
     * @param string  $action        Action to perform OPTIONAL
     * @param array   $params        Additional params OPTIONAL
     * @param string  $interface     Interface script OPTIONAL
     * @param boolean $forceCleanURL Force flag - use Clean URL OPTIONAL
     * @param boolean $forceCuFlag   Force cu flag ?? OPTIONAL
     *
     * @return string
     */
    public static function buildURL(
        $target = '',
        $action = '',
        array $params = array(),
        $interface = null,
        $forceCleanURL = false,
        $forceCuFlag = null
    ) {
        $result = null;
        $cuFlag = null !== $forceCuFlag
            ? $forceCuFlag
            : (LC_USE_CLEAN_URLS && (!\XLite::isAdminZone() || $forceCleanURL));

        if ($cuFlag) {
            $result = static::buildCleanURL($target, $action, $params);
        }

        if (null === $result) {
            if (null === $interface && !$cuFlag) {
                $interface = \XLite::getInstance()->getScript();
            }

            $result = \Includes\Utils\Converter::buildURL($target, $action, $params, $interface);
            if ($cuFlag && !$result) {
                $result = \XLite::getInstance()->getShopURL(
                    $result,
                    null,
                    array()
                );
            }
        }

        if (LC_USE_CLEAN_URLS && \XLite\Core\Router::getInstance()->isUseLanguageUrls() && ($interface == \XLite::CART_SELF || !\XLite::isAdminZone())) {
            $language = \XLite\Core\Session::getInstance()->getLanguage();
            if (!$language->getDefaultAuth() && !preg_match('/^https?:\/\//Ss', $result)) {
                $result = $language->getCode() . '/' . $result;
            }
        }

        return $result;
    }

    /**
     * Compose full URL from target, action and additional params
     *
     * @param string $target    Page identifier OPTIONAL
     * @param string $action    Action to perform OPTIONAL
     * @param array  $params    Additional params OPTIONAL
     * @param string $interface Interface script OPTIONAL
     * @param boolean $forceCuFlag Force clean URL state OPTIONAL
     *
     * @return string
     */
    public static function buildFullURL($target = '', $action = '', array $params = array(), $interface = null, $forceCuFlag = null)
    {
        return \XLite::getInstance()->getShopURL(static::buildURL($target, $action, $params, $interface, false, $forceCuFlag));
    }

    /**
     * Compose URL from target, action and additional params
     *
     * @param \XLite\Model\AccessControlCell $acc
     * @param string                         $target    Page identifier
     * @param string                         $action    Action to perform
     * @param array                          $params    Additional params
     * @param string                         $interface Interface script OPTIONAL
     *
     * @return string
     */
    public static function buildPersistentAccessURL(\XLite\Model\AccessControlCell $acc, $target = '', $action = '', array $params = array(), $interface = null)
    {
        $returnData = [
            'target' => $target,
            'action' => $action,
            'params' => $params
        ];
        
        $acc->mergeReturnData($returnData);
        
        \XLite\Core\Database::getEM()->flush($acc);

        return static::buildFullURL('access_control', '', ['key' => $acc->getHash()], $interface);
    }

    /**
     * Compose clean URL
     *
     * @param string $target Page identifier OPTIONAL
     * @param string $action Action to perform OPTIONAL
     * @param array  $params Additional params OPTIONAL
     *
     * @return string
     */
    public static function buildCleanURL($target = '', $action = '', array $params = array())
    {
        if ($action && empty($params['action'])) {
            $params['action'] = $action;
        }

        return \XLite\Core\Database::getRepo('XLite\Model\CleanURL')->buildURL($target, $params);
    }

    /**
     * Make specified URL a valid for W3C-validation
     *
     * @param string $url URL
     *
     * @return string
     */
    // TODO: Doesn't belong here. Should be done on the templating tier via the (auto)escaping
    // TODO: Remove with all its usages
    public static function makeURLValid($url)
    {
        if (false === strpos($url, '&amp;') && false !== strpos($url, '&')) {
            $url = str_replace('&', '&amp;', $url);
        }

        return $url;
    }

    /**
     * Parse clean URL (<rest>/<last>/<url>(?:\.<ext="htm">(?:l)))
     *
     * @param string $url  Main part of a clean URL
     * @param string $last First part before the "url" OPTIONAL
     * @param string $rest Part before the "url" and "last" OPTIONAL
     * @param string $ext  Extension OPTIONAL
     *
     * @return array
     */
    public static function parseCleanUrl($url, $last = '', $rest = '', $ext = '')
    {
        return \XLite\Core\Database::getRepo('XLite\Model\CleanURL')->parseURL($url, $last, $rest, $ext);
    }

    /**
     * Return pattern to check clean URLs
     *
     * @param boolean $getAllowedPattern Get allowed chars pattern if true,
     *                                   otherwise get unallowed chars pattern OPTIONAL
     *
     * @return string
     */
    public static function getCleanURLAllowedCharsPattern($getAllowedPattern = true)
    {
        return $getAllowedPattern ? '[.\w_\-]+' : '[^.\w_\-]';
    }

    // }}}

    // {{{ Others

    /**
     * Convert to one-dimensional array
     *
     * @param array  $data    Array to flat
     * @param string $currKey Parameter for recursive calls OPTIONAL
     *
     * @return array
     */
    public static function convertTreeToFlatArray(array $data, $currKey = '')
    {
        $result = array();

        foreach ($data as $key => $value) {
            $key = $currKey . (empty($currKey) ? $key : '[' . $key . ']');
            $result += is_array($value) ? self::convertTreeToFlatArray($value, $key) : array($key => $value);
        }

        return $result;
    }

    /**
     * Generate random token (32 chars)
     *
     * @return string
     */
    public static function generateRandomToken()
    {
        return md5(microtime(true) + rand(0, 1000000));
    }

    /**
     * Check - is GDlib enabled or not
     *
     * @return boolean
     */
    public static function isGDEnabled()
    {
        return function_exists('imagecreatefromjpeg')
            && function_exists('imagecreatetruecolor')
            && function_exists('imagealphablending')
            && function_exists('imagesavealpha')
            && function_exists('imagecopyresampled');
    }

    /**
     * Check if specified string is URL or not
     *
     * @param string $url URL
     *
     * @return boolean
     */
    public static function isURL($url)
    {
        static $pattern = '(?:([a-z][a-z0-9\*\-\.]*):\/\/(?:(?:(?:[\w\.\-\+!$&\'\(\)*\+,;=]|%[0-9a-f]{2})+:)*(?:[\w\.\-\+%!$&\'\(\)*\+,;=]|%[0-9a-f]{2})+@)?(?:(?:[a-z0-9\-\.]|%[0-9a-f]{2})+|(?:\[(?:[0-9a-f]{0,4}:)*(?:[0-9a-f]{0,4})\]))(?::[0-9]+)?(?:[\/|\?](?:[\w#!:\.\?\+=&@!$\'~*,;\/\(\)\[\]\-]|%[0-9a-f]{2})*)?)';

        return is_string($url) && 0 < preg_match('/^' . $pattern . '$/Ss', $url);
    }

    /**
     * Check for empty string
     *
     * @param string $string String to check
     *
     * @return boolean
     */
    public static function isEmptyString($string)
    {
        return '' === $string || false === $string;
    }

    /**
     * Return class name without backslashes
     *
     * @param \XLite\Base $obj Object to get class name from
     *
     * @return string
     */
    public static function getPlainClassName(\XLite\Base $obj)
    {
        return str_replace('\\', '', get_class($obj));
    }

    /**
     * Convert value from one to other weight units
     *
     * @param float  $value   Weight value
     * @param string $srcUnit Source weight unit
     * @param string $dstUnit Destination weight unit
     *
     * @return float
     */
    public static function convertWeightUnits($value, $srcUnit, $dstUnit)
    {
        $unitsInGrams = array(
            'lbs' => 453.59,
            'oz'  => 28.35,
            'kg'  => 1000,
            'g'   => 1,
        );

        if (array_diff(array($srcUnit, $dstUnit), array('lbs', 'oz'))) {
            // Conversion between lbs/oz and kg/g
            $multiplier = $unitsInGrams[$srcUnit] / $unitsInGrams[$dstUnit];

        } else {
            // Conversion between lbs and oz
            // To make result more precise...
            $unitsInOz = array(
                'lbs' => 16,
                'oz'  => 1,
            );
            $multiplier = $unitsInOz[$srcUnit] / $unitsInOz[$dstUnit];
        }

        return $value * $multiplier;
    }

    /**
     * Get server timstamp with considering server time zone
     *
     * @param \DateTimeZone|string $timeZone Server time zone OPTIONAL
     *
     * @return integer
     */
    public static function time($timeZone = null)
    {
        if (!empty($timeZone) && is_string($timeZone)) {
            // If timeZone is string create DateTimeZone object
            $timeZone = new \DateTimeZone($timeZone);
        }

        $time = ($timeZone instanceof \DateTimeZone ? new \DateTime('now', $timeZone) : new \DateTime());

        return $time->getTimestamp();
    }

    /**
     * Format time
     *
     * @param integer $base                  UNIX time stamp OPTIONAL
     * @param string  $format                Format string OPTIONAL
     * @param boolean $convertToUserTimeZone True if time value should be converted according to the time zone OPTIONAL
     *
     * @return string
     */
    public static function formatTime($base = null, $format = null, $convertToUserTimeZone = true)
    {
        if (!$format) {
            $config = \XLite\Core\Config::getInstance();
            $format = $config->Units->date_format . ', ' . $config->Units->time_format;
        }

        if ($convertToUserTimeZone) {
            $base = \XLite\Core\Converter::convertTimeToUser($base);
        }

        return static::getStrftime($format, $base);
    }

    /**
     * Format date
     *
     * @param integer $base                  UNIX time stamp OPTIONAL
     * @param string  $format                Format string OPTIONAL
     * @param boolean $convertToUserTimeZone True if time value should be converted according to the time zone OPTIONAL
     *
     * @return string
     */
    public static function formatDate($base = null, $format = null, $convertToUserTimeZone = true)
    {
        if (!$format) {
            $format = \XLite\Core\Config::getInstance()->Units->date_format;
        }

        if ($convertToUserTimeZone) {
            $base = \XLite\Core\Converter::convertTimeToUser($base);
        }

        return static::getStrftime($format, $base);
    }

    /**
     * Parse from js format
     *
     * @param string $value Date value
     * @param string $format Date format in strftime format OPTIONAL
     *
     * @return integer
     */
    public static function parseFromJsFormat($value, $format = null)
    {
        if (!$format) {
            $formats = static::getDateFormatsByStrftimeFormat();
            $format = $formats['phpFormat'];
        }
        $date = \DateTime::createFromFormat($format, $value);

        $timestamp = null;
        if($date) {
            $timestamp = \XLite\Core\Converter::convertTimeToUser(
                $date->getTimestamp()
            );
        }
        return $timestamp;
    }

    /**
     * Get appropriate formats by strftime format
     *
     * @param string $format Date format in strftime format OPTIONAL
     *
     * @return array
     */
    public static function getDateFormatsByStrftimeFormat($format = null)
    {
        $formats = static::getAvailableDateFormats();

        if (!$format) {
            $format = \XLite\Core\Config::getInstance()->Units->date_format;
        }

        return $formats[$format];
    }

    /**
     * Allowed date formats
     *
     * @return array
     */
    public static function getAvailableDateFormats()
    {
        return array(
            '%m/%d/%Y' => array(
                'jsFormat'  => 'mm/dd/yy',
                'phpFormat'  => 'm/d/Y',
            ),
            '%m-%d-%Y' => array(
                'jsFormat'  => 'mm-dd-yy',
                'phpFormat'  => 'm-d-Y',
            ),
            '%d.%m.%Y' => array(
                'jsFormat'  => 'dd.mm.yy',
                'phpFormat'  => 'd.m.Y',
            ),
            '%d-%m-%Y' => array(
                'jsFormat'  => 'dd-mm-yy',
                'phpFormat'  => 'd-m-Y',
            ),
            '%d/%m/%Y' => array(
                'jsFormat'  => 'dd/mm/yy',
                'phpFormat'  => 'd/m/Y',
            ),
            '%Y-%m-%d' => array(
                'jsFormat'  => 'yy-mm-dd',
                'phpFormat'  => 'Y-m-d',
            ),
            '%b %e, %Y' => array(
                'jsFormat'  => 'M d, yy',
                'phpFormat'  => 'M d, Y',
            ),
            '%A, %B %e, %Y' => array(
                'jsFormat'  => 'DD, MM d, yy',
                'phpFormat'  => 'l, F d, Y',
            )
        );
    }

    /**
     * Format day time
     *
     * @param integer $base                  UNIX time stamp OPTIONAL
     * @param string  $format                Format string OPTIONAL
     * @param boolean $convertToUserTimeZone True if time value should be converted according to the time zone OPTIONAL
     *
     * @return string
     */
    public static function formatDayTime($base = null, $format = null, $convertToUserTimeZone = true)
    {
        if (!$format) {
            $format = \XLite\Core\Config::getInstance()->Units->time_format;
        }

        if ($convertToUserTimeZone) {
            $base = \XLite\Core\Converter::convertTimeToUser($base);
        }

        return static::getStrftime($format, $base);
    }

    /**
     * Get strftime() with specified format and timestamp value
     *
     * @param string  $format Format string
     * @param integer $base   UNIX time stamp OPTIONAL
     *
     * @return string
     */
    protected static function getStrftime($format, $base = null)
    {
        static::setLocaleToUTF8();

        $win = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';

        if ($win) {
            $format = str_replace('%e', '%#d', $format);
        }

        $date = null !== $base ? strftime($format, $base) : strftime($format);

        if ($win) {
            $locale = setlocale(LC_TIME, 0);

            if (preg_match('/(([^_]+)_?([^.]*))\.?(.*)?/', $locale, $match)
                && preg_match('/^.+_.+\.(\d+)$/', $match[0], $match)
                && \XLite\Core\Iconv::getInstance()->isValid()
            ) {
                $date = \XLite\Core\Iconv::getInstance()->convert('WINDOWS-' . $match[1], 'UTF-8', $date) ?: $date;

            } elseif (!empty(static::$charsetsTable[$locale])) {
                $date = \XLite\Core\Iconv::getInstance()->convert(static::$charsetsTable[$locale], 'UTF-8', $date)
                    ?: $date;
            }
        }

        return $date;
    }

    /**
     * Attempt to set locale to UTF-8
     *
     * @return void
     */
    protected static function setLocaleToUTF8()
    {
        if (!self::$isLocaleSet
            && preg_match('/(([^_]+)_?([^.]*))\.?(.*)?/', setlocale(LC_TIME, 0), $match)
            && !preg_match('/utf\-?8/i', $match[4])
        ) {
            $lng = \XLite\Core\Session::getInstance()->getLanguage();
            $localeCode = $lng->getCode() . '_' . strtoupper($lng->getCode());
            $localeCode3 = $lng->getCode3();

            setlocale(
                LC_TIME,
                $localeCode . '.UTF-8',
                $localeCode,
                $lng->getCode(),
                $localeCode3,
                $match[0]
            );

            self::$isLocaleSet = true;
        }
    }

    // }}}

    // {{{ File size

    /**
     * Prepare human-readable output for file size
     *
     * @param integer $size Size in bytes
     *
     * @return string
     */
    public static function formatFileSize($size)
    {
        list($size, $suffix) = \Includes\Utils\Converter::formatFileSize($size);

        return $size . ' ' . ($suffix ? static::t($suffix) : '');
    }

    /**
     * Get maximum allowed file size of uploading file
     *
     * @return integer
     */
    public static function getUploadFileMaxSize()
    {
        return min(
            static::convertShortSize(ini_get('upload_max_filesize')),
            static::convertShortSize(ini_get('post_max_size'))
        );
    }

    /**
     * Convert short size (2M, 8K) to human readable
     *
     * @param string $size Shortsize
     *
     * @return string
     */
    public static function convertShortSizeToHumanReadable($size)
    {
        $size = static::convertShortSize($size);

        if (static::GIGABYTE < $size) {
            $label = 'X GB';
            $size = round($size / static::GIGABYTE, 3);

        } elseif (static::MEGABYTE < $size) {
            $label = 'X MB';
            $size = round($size / static::MEGABYTE, 3);

        } elseif (static::KILOBYTE < $size) {
            $label = 'X kB';
            $size = round($size / static::KILOBYTE, 3);

        } else {
            $label = 'X bytes';
        }

        return \XLite\Core\Translation::lbl($label, array('value' => $size));
    }

    /**
     * Convert short size (2M, 8K) to normal size (in bytes)
     *
     * @param string $size Short size
     *
     * @return integer
     */
    public static function convertShortSize($size)
    {
        if (preg_match('/^(\d+)([a-z])$/Sis', $size, $match)) {
            $size = (int) $match[1];
            switch ($match[2]) {
                case 'G':
                    $size *= 1073741824;
                    break;

                case 'M':
                    $size *= 1048576;
                    break;

                case 'K':
                    $size *= 1024;
                    break;

                default:
            }

        } else {
            $size = (int) $size;
        }

        return $size;
    }

    // }}}

    // {{{ Time

    /**
     * Convert user time to server time
     *
     * @param integer $time User time
     *
     * @return integer
     */
    public static function convertTimeToServer($time)
    {
        $server = new \DateTime();
        $server = $server->getTimezone()->getOffset($server);

        $user = new \DateTime();
        $timeZone = \XLite\Core\Config::getInstance()->Units->time_zone ?: $user->getTimezone()->getName();
        $user->setTimezone(new \DateTimeZone($timeZone));
        $user = $user->getTimezone()->getOffset($user);

        $offset = $server - $user;

        return $time + $offset;
    }

    /**
     * Convert server time to user time
     *
     * @param integer $time Server time
     *
     * @return integer
     */
    public static function convertTimeToUser($time = null)
    {
        if (null === $time) {
            $time = \XLite\Core\Converter::time();
        }

        $server = new \DateTime();
        $server = $server->getTimezone()->getOffset($server);

        $user = new \DateTime();
        $timeZone = \XLite\Core\Config::getInstance()->Units->time_zone ?: $user->getTimezone()->getName();
        $user->setTimezone(new \DateTimezone($timeZone));
        $user = $user->getTimezone()->getOffset($user);

        $offset = $server - $user;

        return $time - $offset;
    }

    /**
     * Returns start of the day
     *
     * @param integer $time Server time
     *
     * @return integer
     */
    public static function getDayStart($time = null)
    {
        if (null === $time) {
            $time = \XLite\Core\Converter::time();
        }

        return mktime(0, 0, 0, date('n', $time), date('j', $time), date('Y', $time));
    }

    /**
     * Returns end of the day
     *
     * @param integer $time Server time
     *
     * @return integer
     */
    public static function getDayEnd($time = null)
    {
        if (null === $time) {
            $time = \XLite\Core\Converter::time();
        }

        return mktime(23, 59, 59, date('n', $time), date('j', $time), date('Y', $time));
    }

    // }}}

    // {{{ Locale detection methods

    /**
     * Get locale value for specified language code (to use in setlocale() function)
     *
     * @param string $code Language code OPTIONAL
     *
     * @return string
     */
    public static function getLocaleByCode($code = null)
    {
        $result = 0;

        if (is_null($code)) {
            $code = \XLite\Core\Session::getInstance()->getLanguage()->getCode();
        }

        if ($code) {

            if (!isset(static::$localesCache[$code])) {
                static::$localesCache[$code] = static::detectLocaleByCode($code);
            }

            $result = static::$localesCache[$code];
        }

        return $result;
    }

    /**
     * Detect and return correct locale value for specified language code
     *
     * @param string $code Language code
     *
     * @return string
     */
    public static function detectLocaleByCode($code)
    {
        $result = 0;

        foreach (static::getDetectLocaleMethods() as $m) {
            $method = 'detectLocaleBy' . static::convertToCamelCase($m);
            if (method_exists('\XLite\Core\Converter', $method)) {
                $locale = static::$method($code);
                if ($locale) {
                    $result = $locale;
                    break;
                }
            }
        }

        return $result;
    }

    /**
     * Get locale by default detection method
     *
     * @param string $code Language code
     *
     * @return string
     */
    public static function detectLocaleByDefault($code)
    {
        $code2 = ('en' == $code ? 'US' : strtoupper($code));

        return $code . '_' . $code2 . '.' . 'utf8';
    }

    /**
     * Detect list of allowed locales via system commands and get closest locale
     *
     * @param string $code Language code
     *
     * @return string
     */
    public static function detectLocaleBySystem($code)
    {
        $locales = static::getSystemLocales();

        return $locales ? static::searchClosestLocale($code, $locales) : null;
    }

    public static function getSystemLocales()
    {
        $cacheKey       = 'system_locales_for_detectLocaleBySystem';
        $ttl            = 3600;
        $cacheDriver    = \XLite\Core\Database::getCacheDriver();

        $locales = $cacheDriver->fetch($cacheKey);
        if (!$locales) {
            $locales = array();

            $cmd = 'locale -a';

            if (function_exists('exec')) {
                exec($cmd, $locales);
            }

            $cacheDriver->save($cacheKey, $locales, $ttl);
        }

        return $locales;
    }

    /**
     * Return locale by language code
     *
     * @param string $code    Language code
     *
     * @return string
     */
    public static function langToLocale($code)
    {
        $predefinedLocales = static::getPredefinedLanguageLocales();

        return isset($predefinedLocales[$code]) ? $predefinedLocales[$code] : $code;
    }

    /**
     * Return predefined locales list
     *
     * @return array
     */
    public static function getPredefinedLanguageLocales()
    {
        return [
            'gb' => 'en-GB'
        ];
    }

    /**
     * Detect closest locale by language code from the specified list of locales
     *
     * @param string $code    Language code
     * @param array  $locales List of available locales
     *
     * @return string
     */
    public static function searchClosestLocale($code, $locales)
    {
        $result = null;
        $result2 = null;

        $code = strtolower($code);

        // Get country code
        $country = static::getCurrentCountry();

        if ($country) {
            $country = strtoupper($country);

        } else {
            $country = ('en' == $code ? 'US' : strtoupper($code));
        }

        foreach ($locales as $locale) {
            if ($country) {
                if (preg_match('/^' . preg_quote($code) . '_' . preg_quote($country) . '/', $locale)) {
                    // Exact match
                    $result = $locale;
                    break;
                }
            }
            if (!$result2 && preg_match('/^' . preg_quote($code) . '/', $locale)) {
                // Closest match - we will get first match then break search
                $result2 = $locale;
                if (!$country) {
                    // Just break if there are no country defined
                    break;
                }
            }
        }

        return $result ?: $result2;
    }

    /**
     * Get current country code
     *
     * @return string
     */
    public static function getCurrentCountry()
    {
        return null;
    }

    /**
     * Get list of methods to detect locale
     *
     * @return array
     */
    protected static function getDetectLocaleMethods()
    {
        return array('system', 'default');
    }

    // }}}
}
