<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Includes;

/**
 * ErrorHandler
 *
 * @package XLite
 */
abstract class ErrorHandler
{
    /**
     * Common error codes
     */
    const ERROR_UNKNOWN          = -1;
    const ERROR_FATAL_ERROR      = 2;
    const ERROR_MAINTENANCE_MODE = -9999;
    const ERROR_NOT_INSTALLED    = -8888;
    const ERROR_CLOSED           = -7777;

    /**
     * Error page types
     */
    const ERROR_PAGE_TYPE_ERROR         = 'error';
    const ERROR_PAGE_TYPE_MAINTENANCE   = 'maintenance';
    const ERROR_PAGE_TYPE_NOT_INSTALLED = 'install';
    const ERROR_PAGE_TYPE_CLOSED        = 'closed';

    /**
     * HTTP codes
     */
    const HTTP_CODE_ERROR   = 500;
    const HTTP_CODE_UNABLE  = 503;

    /**
     * Throw exception
     *
     * @param string  $message Error message
     * @param integer $code    Error code
     *
     * @return void
     */
    protected static function throwException($message, $code)
    {
        throw new \Exception($message, $code);
    }

    /**
     * Add info to a log file
     *
     * @param string  $message   Error message
     * @param integer $code      Error code
     * @param string  $backtrace Stack trace OPTIONAL
     *
     * @return void
     */
    protected static function logInfo($message, $code, $backtrace = null)
    {
        if (
            !isset($backtrace)
            && 'cli' != PHP_SAPI
        ) {
            $logLevel = \Includes\Utils\ConfigParser::getOptions(array('log_details', 'level'));
            $backtrace = static::getBacktrace(LOG_DEBUG === intval($logLevel), true);
        }

        $message = date('[d-M-Y H:i:s]') . ' Error (code: ' . $code . '): ' . $message . PHP_EOL;

        // Add additional info
        $parts = array(
            'Server API: ' . PHP_SAPI,
        );

        if (!empty($_SERVER)) {
            if (isset($_SERVER['REQUEST_METHOD'])) {
                $parts[] = 'Request method: ' . $_SERVER['REQUEST_METHOD'];
            }

            if (isset($_SERVER['REQUEST_URI'])) {
                $parts[] = 'URI: ' . $_SERVER['REQUEST_URI'];
            }
        }

        $message .= implode(';' . PHP_EOL, $parts) . ';' . PHP_EOL;
        if ($backtrace && $code != static::ERROR_CLOSED) {
            $message .= 'Backtrace: ' . PHP_EOL . $backtrace . PHP_EOL . PHP_EOL;
        }

        \Includes\Utils\FileManager::write(static::getLogFile($code), $message, FILE_APPEND);
    }

    /**
     * Get backtrace
     *
     * @param boolean $withArgs With arguments
     * @param boolean $pretty   Pretty output   OPTIONAL
     *
     * @return string
     */
    protected static function getBacktrace($withArgs, $pretty = false)
    {
        $result = '';

        if ($pretty) {
            $backtraceRaw = !$withArgs && defined('DEBUG_BACKTRACE_IGNORE_ARGS')
                ? debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS)
                : debug_backtrace();

            array_shift($backtraceRaw);
            foreach ($backtraceRaw as $key => $backtraceLine) {
                $result .= static::getBacktraceLine($key, $backtraceLine);
            }

        } else {
            ob_start();

            if (!$withArgs && defined('DEBUG_BACKTRACE_IGNORE_ARGS')) {
                debug_print_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
            } else {
                debug_print_backtrace();
            }

            $backtrace = ob_get_contents();
            ob_end_clean();
        }

        return $result;
    }

    /**
     * Format backtraceline
     *
     * @param string    $key            Key
     * @param array     $backtraceLine  Backtrace line info
     *
     * @return string
     */
    protected static function getBacktraceLine($key, $backtraceLine)
    {
        $classFunc = '';

        if (isset($backtraceLine['class'])) {
            $classFunc .= $backtraceLine['class'];
        }
        if (isset($backtraceLine['type']) && isset($backtraceLine['function'])) {
            $classFunc .= $backtraceLine['type'] . $backtraceLine['function'] . '()';
        }


        $fileLine = '';
        if (isset($backtraceLine['file'])) {
            $fileLine .= $backtraceLine['file'];
        }
        if (isset($backtraceLine['line'])) {
            $fileLine .= ':' . $backtraceLine['line'];
        }

        if ($fileLine) {
            $fileLine = ' called at [' . $fileLine . ']';
        }

        $args = isset($backtraceLine['args']) && $backtraceLine['args']
            ? var_export($backtraceLine['args'], true)
            : '';

        if ($args) {
            $args = PHP_EOL . 'With args:' . PHP_EOL . $args;
        }

       return sprintf('#%s %s %s %s',
            $key, $classFunc, $fileLine, $args
        ) . PHP_EOL;
    }

    /**
     * Return path to the log file
     *
     * @pram integer $code Error code
     *
     * @return string
     */
    protected static function getLogFile($code = 0)
    {
        switch ($code) {
            case static::ERROR_CLOSED:
            case static::ERROR_MAINTENANCE_MODE:
                $path = LC_DIR_VAR . 'log' . LC_DS . 'actions.log.' . date('Y-m-d') . '.php';
                break;

            default:
                $path = LC_DIR_VAR . 'log' . LC_DS . 'php_errors.log.' . date('Y-m-d') . '.php';
        }

        return $path;
    }

    /**
     * Return name of the error page file (.html)
     *
     * @return string
     */
    protected static function getErrorPageFileDefault()
    {
        return \Includes\Utils\Session::issetAdminCookie()
            ? ('public' . LC_DS . 'error.html')
            : ('public' . LC_DS . 'customer' . LC_DS . 'error.html');
    }

    /**
     * Return name of the error page file (.html)
     *
     * @return string
     */
    protected static function getErrorPageFileFromConfig()
    {
        return \Includes\Utils\Session::issetAdminCookie()
            ? \Includes\Utils\ConfigParser::getOptions(array('error_handling', 'page'))
            : \Includes\Utils\ConfigParser::getOptions(array('error_handling', 'page_customer'));
    }

    /**
     * Return name of the maintenance page file (.html)
     *
     * @return string
     */
    protected static function getMaintenancePageFileDefault()
    {
        return 'public' . LC_DS . 'maintenance.html';
    }

    /**
     * Return name of the maintenance page file (.html)
     *
     * @return string
     */
    protected static function getMaintenancePageFileFromConfig()
    {
        return \Includes\Utils\ConfigParser::getOptions(array('error_handling', 'maintenance'));
    }

    /**
     * Return name of the error page file (.html)
     *
     * @return string
     */
    protected static function getNotInstalledPageFile()
    {
        return 'public' . LC_DS . 'install.html';
    }

    /**
     * Return name of the error page file (.html)
     *
     * @return string
     */
    protected static function getClosedPageFile()
    {
        return 'public' . LC_DS . 'closed.html';
    }

    /**
     * Return name of the error page file (.html)
     *
     * @return string
     */
    protected static function getErrorPageFile($type = self::ERROR_PAGE_TYPE_ERROR)
    {
        if (self::ERROR_PAGE_TYPE_MAINTENANCE == $type) {
            $file = LC_DIR_ROOT . (static::getMaintenancePageFileFromConfig() ?: static::getMaintenancePageFileDefault());

        } elseif (self::ERROR_PAGE_TYPE_NOT_INSTALLED == $type) {
            $file = LC_DIR_ROOT . static::getNotInstalledPageFile();

        } elseif (self::ERROR_PAGE_TYPE_CLOSED == $type) {
            $file = LC_DIR_ROOT . static::getClosedPageFile();

        } else {
            $file = LC_DIR_ROOT . (static::getErrorPageFileFromConfig() ?: static::getErrorPageFileDefault());
        }

        return $file;
    }

    /**
     * Return content of the error page file (.html)
     *
     * @return string
     */
    protected static function getErrorPageFileContent($type = self::ERROR_PAGE_TYPE_ERROR)
    {
        return \Includes\Utils\FileManager::read(static::getErrorPageFile($type)) ?: LC_ERROR_PAGE_MESSAGE;
    }

    /**
     * Return content of the error page file (.html)
     *
     * @return string
     */
    protected static function getErrorPage($type = self::ERROR_PAGE_TYPE_ERROR)
    {
        return preg_replace_callback(
            '/@URL([^@]+)@/Ss',
            array(get_called_class(), 'processURL'),
            static::getErrorPageFileContent($type)
        );
    }

    /**
     * Process URL
     *
     * @param array $matches Replace matches
     *
     * @return string
     */
    public static function processURL(array $matches)
    {
        return \Includes\Utils\URLManager::getShopURL($matches[1]);
    }

    /**
     * Show error message (page)
     *
     * @param mixed  $code    Error code
     * @param string $message Error message
     * @param string $page    Error page or message template
     *
     * @return void
     */
    protected static function showErrorPage($code, $message, $page = null, $prefix = 'ERROR_', $http_code = null)
    {
        if (null === $http_code) {
            $http_code = static::getErrorPageType($code) !== static::ERROR_PAGE_TYPE_ERROR
                ? static::HTTP_CODE_UNABLE
                : static::HTTP_CODE_ERROR;
        }

        showErrorPage(
            $code,
            $message,
            $page
            ?: (
                LC_IS_CLI_MODE
                ? LC_ERROR_PAGE_MESSAGE
                : static::getErrorPage(static::getErrorPageType($code))
            ),
            $prefix,
            static::getErrorPageType($code) !== static::ERROR_PAGE_TYPE_ERROR
                ? 503
                : $http_code
        );
    }

    /**
     * Return content of the error page file (.html)
     *
     * @return string
     */
    protected static function getErrorPageType($code)
    {
        $result = self::ERROR_PAGE_TYPE_ERROR;

        if (self::ERROR_MAINTENANCE_MODE == $code) {
            $result = self::ERROR_PAGE_TYPE_MAINTENANCE;

        } elseif (self::ERROR_NOT_INSTALLED == $code) {
            $result = self::ERROR_PAGE_TYPE_NOT_INSTALLED;

        } elseif (self::ERROR_CLOSED == $code) {
            $result = self::ERROR_PAGE_TYPE_CLOSED;
        }

        return $result;
    }

    /**
     * Shutdown function
     *
     * @return void
     */
    public static function shutdown()
    {
        static::handleError(error_get_last() ?: array());
    }

    /**
     * Error handler
     *
     * @param array $error catched error
     *
     * @return void
     */
    public static function handleError(array $error)
    {
        \Includes\Decorator\Utils\CacheManager::checkRebuildIndicatorState();

        if (isset($error['type']) && E_ERROR == $error['type']) {
            static::logInfo($error['message'], $error['type']);
            static::showErrorPage(__CLASS__ . '::ERROR_FATAL_ERROR', $error['message']);
        }
    }

    /**
     * Handle common errorr
     *
     * @param integer $errno   Error type
     * @param string  $errstr  Error message
     * @param string  $errfile File
     * @param integer $errline Line number
     *
     * @return void
     */
    public static function handleCommonError($errno, $errstr, $errfile = null, $errline = null)
    {
        if ($errfile && $errline) {
            $errstr .= ' in file ' . $errfile . ' : ' . $errline;
        }

        static::logInfo($errstr, $errno);
    }

    /**
     * Exception handler
     *
     * @param \Exception|\Error $exception catched exception
     *
     * @return void
     */
    public static function handleException($exception)
    {
        static::logInfo($exception->getMessage(), $exception->getCode(), $exception->getTraceAsString());
        static::showErrorPage($exception->getCode(), $exception->getMessage());
    }

    /**
     * Provoke an error
     *
     * @param string  $message Error message
     * @param integer $code    Error code
     *
     * @return void
     */
    public static function fireError($message, $code = self::ERROR_UNKNOWN)
    {
        static::throwException($message, $code);
    }

    /**
     * Method to display certain error
     *
     * @param string $method Name of an abstract method
     *
     * @return void
     */
    public static function fireErrorAbstractMethodCall($method)
    {
        static::fireError('Abstract method call: ' . $method);
    }

    /**
     * Check if LC is installed
     *
     * @return void
     */
    public static function checkIsLCInstalled()
    {
        if (!\Includes\Utils\ConfigParser::getOptions(array('database_details', 'database'))) {

            $link = \Includes\Utils\URLManager::getShopURL('install.php');
            $message = \Includes\Utils\ConfigParser::getInstallationLng() === 'ru'
                ? '<a href="' . $link . '">Запустите установку магазина</a>'
                : '<a href="' . $link . '">Click here</a> to run the installation wizard.';

            static::showErrorPage(self::ERROR_NOT_INSTALLED, $message);
        }
    }
}
