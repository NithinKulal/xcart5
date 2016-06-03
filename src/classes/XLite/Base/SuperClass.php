<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Base;

/**
 * SuperClass
 */
abstract class SuperClass
{
    /**
     * Default store language
     *
     * @var string
     */
    protected static $defaultLanguage;
    
    /**
     * Timestamp of the user timezone
     *
     * @var integer
     */
    protected static $userTime;

    /**
     * So called static constructor
     *
     * @return void
     */
    public static function __constructStatic()
    {
        static::$defaultLanguage = \Includes\Utils\ConfigParser::getOptions(array('language', 'default'));
    }

    /**
     * Set default language
     *
     * @param string $code Language code
     *
     * @return void
     */
    public static function setDefaultLanguage($code)
    {
        static::$defaultLanguage = $code;
    }

    /**
     * Getter
     *
     * @return string
     */
    public static function getDefaultLanguage()
    {
        return static::$defaultLanguage;
    }

    /**
     * Return converted into user time current timestamp
     * 
     * @return integer
     */
    public static function getUserTime()
    {
        if (!isset(static::$userTime)) {
            static::$userTime = \XLite\Core\Converter::convertTimeToUser();
        }
        return static::$userTime;
    }

    /**
     * Language label translation short method
     *
     * @param string $name      Label name
     * @param array  $arguments Substitution arguments OPTIONAL
     * @param string $code      Language code OPTIONAL
     *
     * @return string
     */
    protected static function t($name, array $arguments = array(), $code = null)
    {
        return \XLite\Core\Translation::getInstance()->translate($name, $arguments, $code);
    }

    /**
     * Protected constructor.
     * It's not possible to instantiate a derived class (using the "new" operator)
     * until that child class is not implemented public constructor
     *
     * @return void
     */
    protected function __construct()
    {
    }

    /**
     * Stop script execution
     *
     * :FIXME: - must be static
     *
     * @param string $message Text to display
     *
     * @return void
     */
    protected function doDie($message)
    {
        if (!($this instanceof \XLite\Logger)) {
            \XLite\Logger::getInstance()->log($message, LOG_ERR);
        }

        if (
            $this instanceof XLite
            || \XLite::getInstance()->getOptions(array('log_details', 'suppress_errors'))
        ) {
            $message = 'Internal error. Contact the site administrator.';
        }

        die ($message);
    }
}

// Call static constructor
\XLite\Base\SuperClass::__constructStatic();