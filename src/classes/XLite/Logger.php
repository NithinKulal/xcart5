<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite;

/**
 * Logger
 */
class Logger extends \XLite\Base\Singleton
{
    /**
     * Log file name regexp pattern
     */
    const LOG_FILE_NAME_PATTERN = '/^[a-zA-Z_]+\.log\.\d{4}-\d{2}-\d{2}\.php$/Ss';

    /**
     * Security file header
     *
     * @var string
     */
    protected $securityHeader = '<?php die(1); ?>';

    /**
     * Hash errors
     *
     * @var array
     */
    protected static $hashErrors = array();

    /**
     * Errors translate table (PHP -> PEAR)
     *
     * @var array
     */
    protected $errorsTranslate = null;

    /**
     * PHP error names
     *
     * @var array
     */
    protected $errorTypes = null;

    /**
     * Options
     *
     * @var array
     */
    protected $options = array(
        'type'  => null,
        'name'  => '/dev/null',
        'level' => LOG_WARNING,
        'ident' => 'X-Lite',
    );

    /**
     * Runtime id
     *
     * @var string
     */
    protected static $runtimeId;

    /**
     * Mark templates flag
     *
     * @var boolean
     */
    protected static $markTemplates = false;


    /**
     * Check - display debug templates info or not
     *
     * @return boolean
     */
    public static function isMarkTemplates()
    {
        return self::$markTemplates;
    }

    /**
     * Constructor
     *
     * @return void
     */
    public function __construct()
    {
        include_once LC_DIR_LIB . 'Log.php';

        $this->options = array_merge(
            $this->options,
            \XLite::getInstance()->getOptions('log_details')
        );

        set_error_handler(array($this, 'registerPHPError'));
        // set_exception_handler(array($this, 'registerException'));

        // Default log path
        $path = $this->getErrorLogPath();
        ini_set('error_log', $path);
        $this->checkLogSecurityHeader($path);

        if (isset($this->options['suppress_errors']) && $this->options['suppress_errors']) {
            ini_set('display_errors', 0);
            ini_set('display_startup_errors', 0);

        } else {
            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);
        }

        if (isset($this->options['suppress_log_errors']) && $this->options['suppress_log_errors']) {
            ini_set('log_errors', 0);

        } else {
            ini_set('log_errors', 1);
        }

        self::$markTemplates = (bool)\XLite::getInstance()->getOptions(array('debug', 'mark_templates'));

        $logger = \Log::singleton(
            $this->getType(),
            $this->getName(),
            $this->getIdent()
        );

        if (isset($this->options['level'])) {
            $level = $this->options['level'];
            if (defined($level)) {
                $level = constant($level);
            }
            $level = min(7, intval($level));
            $mask = 0;
            for ($i = 0; $i <= $level; $i++) {
                $mask += 1 << $i;
            }

            $logger->setMask($mask);
        }
    }

    /**
     * Add log record
     *
     * @param string $message Message
     * @param string $level   Level code OPTIONAL
     * @param array  $trace   Back trace OPTIONAL
     *
     * @return void
     */
    public function log($message, $level = LOG_DEBUG, array $trace = array())
    {
        $dir = getcwd();
        chdir(LC_DIR);

        $logger = \Log::singleton(
            $this->getType(),
            $this->getName(),
            $this->getIdent()
        );

        // Add additional info
        $parts = array(
            'Runtime id: ' . static::getRuntimeId(),
            'Server API: ' . PHP_SAPI . '; IP: ' . (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'n/a'),
        );

        if (isset($_SERVER)) {
            if (isset($_SERVER['REQUEST_METHOD'])) {
                $parts[] = 'Request method: ' . $_SERVER['REQUEST_METHOD'];
            }

            if (isset($_SERVER['REQUEST_URI'])) {
                $parts[] = 'URI: ' . $_SERVER['REQUEST_URI'];
            }
        }

        $message .= PHP_EOL . implode(';' . PHP_EOL, $parts) . ';';

        // Add debug backtrace
        if (LOG_ERR >= $level) {
            $backTrace = $trace ? $this->prepareBackTrace($trace) : static::getBackTrace();
            $message .= PHP_EOL . 'Backtrace:' . PHP_EOL . "\t" . implode(PHP_EOL . "\t", $backTrace);
        }

        $logger->log(trim($message) . PHP_EOL, $level);

        chdir($dir);
    }

    /**
     * Register PHP error
     *
     * @param integer $errno   Error code
     * @param string  $errstr  Error message
     * @param string  $errfile File path
     * @param integer $errline Line number
     *
     * @return boolean
     */
    public function registerPHPError($errno, $errstr, $errfile, $errline)
    {
        $hash = $errno . ':' . $errfile . ':' . $errline;

        if (
            ini_get('error_reporting') & $errno
            && (0 != ini_get('display_errors') || 0 != ini_get('log_errors'))
            && 0 != error_reporting()
            && (1 != ini_get('ignore_repeated_errors') || !isset(self::$hashErrors[$hash]))
        ) {

            $errortype = $this->getPHPErrorName($errno);

            $message = $errortype . ': ' . $errstr . ' in ' . $errfile . ' on line ' . $errline;

            // Display error
            if (0 != ini_get('display_errors')) {
                $displayMessage = $message;

                if (isset($_SERVER['REQUEST_METHOD'])) {
                    $displayMessage = '<strong>' . $errortype . '</strong>: ' . $errstr
                        . ' in <strong>' . $errfile . '</strong> on line <strong>' . $errline . '</strong><br />';
                }

                echo ($displayMessage . PHP_EOL);
            }

            // Save to log
            if (0 != ini_get('log_errors')) {
                $this->log($message, $this->convertPHPErrorToLogError($errno));
            }

            // Save to cache
            if (1 == ini_get('ignore_repeated_errors')) {
                self::$hashErrors[$hash] = true;
            }
        }

        return true;
    }

    /**
     * Register non-catched exception
     *
     * @param \Exception $exception Exception
     *
     * @return void
     */
    public function registerException(\Exception $exception)
    {
        if (
            ini_get('error_reporting') & E_ERROR
            && (0 != ini_get('display_errors') || 0 != ini_get('log_errors'))
            && 0 != error_reporting()
        ) {

            $message = 'Exception: ' . $exception->getMessage()
                . ' in ' . $exception->getFile() . ' on line ' . $exception->getLine();

            // Display error
            if (0 != ini_get('display_errors')) {

                if (isset($_SERVER['REQUEST_METHOD'])) {
                    $displayMessage = '<strong>Exception</strong>: ' . $exception->getMessage()
                        . ' in <strong>' . $exception->getFile() . '</strong>'
                        . ' on line <strong>' . $exception->getLine() . '</strong><br />';
                } else {
                    $displayMessage = $message;
                }

                echo ($displayMessage . PHP_EOL);
            }

            // Save to log
            if (0 != ini_get('log_errors')) {
                $this->log($message, LOG_ERR, $exception->getTrace());
            }
        }
    }

    /**
     * Check security header for specified file
     *
     * @param string $path File path
     *
     * @return void
     */
    public function checkLogSecurityHeader($path)
    {
        if (!file_exists(dirname($path))) {
            \Includes\Utils\FileManager::mkdirRecursive(dirname($path));
        }

        if (!file_exists($path) || $this->securityHeader > filesize($path)) {
            file_put_contents($path, $this->securityHeader . "\n");
        }
    }

    /**
     * Log custom message
     *
     * @param string  $type         Message type
     * @param string  $message      Message
     * @param boolean $useBackTrace User backtrace flag OPTIONAL
     * @param integer $slice        Trace slice count OPTIONAL
     *
     * @return string
     */
    public static function logCustom($type, $message, $useBackTrace = false, $backTraceSlice = 2)
    {
        $type = preg_replace('/[^a-zA-Z0-9_-]/Ss', '', strval($type ?: 'debug'));
        $path = static::getCustomLogPath($type);
        $header = static::getLogFileHeader();
        if (!file_exists($path) || strlen($header) > filesize($path)) {
            @file_put_contents($path, $header);
        }

        if (!is_string($message)) {
            $message = var_export(static::prepareData($message), true);
        }

        $message = trim('[' . @date('H:i:s.u') . '] ' . $message) . PHP_EOL
            . 'Runtime id: ' . static::getRuntimeId() . PHP_EOL
            . 'SAPI: ' . PHP_SAPI . '; '
            . 'IP: ' . (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'n/a') . PHP_EOL
            . 'URI: ' . (isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : 'n/a') . PHP_EOL
            . 'Method: ' . (isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'n/a') . PHP_EOL;

        // Add debug backtrace
        if ($useBackTrace) {
            $backTrace = static::getBackTrace($backTraceSlice);
            $message .= 'Backtrace:' . PHP_EOL . "\t" . implode(PHP_EOL . "\t", $backTrace) . PHP_EOL;
        }

        $message .= PHP_EOL;

        @file_put_contents($path, $message, FILE_APPEND);

        return $path;
    }

    /**
     * Prepare data for logging
     *
     * @param mixed $data
     *
     * @return mixed
     */
    protected static function prepareData($data)
    {
        if (is_array($data)) {
            foreach ($data as $k => $v) {
                $data[$k] = static::prepareData($v);
            }

        } elseif (is_object($data)) {
            $data = \Doctrine\Common\Util\Debug::export($data, 2);
        }

        return $data;
    }

    /**
     * Get custom log URL
     *
     * @param string $type Type
     *
     * @return string
     */
    public static function getCustomLogURL($type)
    {
        return \XLite\Core\Converter::buildURL('log', '', array('log' => static::getCustomLogFileName($type)));
    }

    /**
     * Get custom log file path
     *
     * @param string $type Type
     *
     * @return string
     */
    public static function getCustomLogPath($type)
    {
        return LC_DIR_LOG . static::getCustomLogFileName($type);
    }

    /**
     * Get custom log file name
     *
     * @param string $type Type
     *
     * @return string
     */
    public static function getCustomLogFileName($type)
    {
        return $type . '.log.' . date('Y-m-d') . '.php';
    }

    /**
     * Get log file header
     *
     * @return string
     */
    protected static function getLogFileHeader()
    {
        return '<' . '?php die(); ?' . '>' . PHP_EOL;
    }

    /**
     * Get runtime id
     *
     * @return string
     */
    protected static function getRuntimeId()
    {
        if (!isset(static::$runtimeId)) {
            static::$runtimeId = hash('md4', uniqid('runtime', true), false);
        }

        return static::$runtimeId;
    }

    /**
     * Get log type
     *
     * @return mixed
     */
    protected function getType()
    {
        return $this->options['type'];
    }

    /**
     * Get logger name
     *
     * @return string
     */
    protected function getName()
    {
        $result = $this->options['name'];

        if ('file' == $this->getType()) {
            $dir = dirname(LC_DIR . LC_DS . ltrim($result, LC_DS));
            $file = basename($result);
            $parts = explode('.', $file);
            array_splice($parts, count($parts) - 1, 0, date('Y-m-d'));
            $result = $dir . LC_DS . implode('.', $parts);
            if (!preg_match('/\.php$/Ss', $result)) {
                $result .= '.php';
            }

            $this->checkLogSecurityHeader($result);
        }

        return $result;
    }

    /**
     * Get logger identtificator
     *
     * @return string
     */
    protected function getIdent()
    {
        return $this->options['ident'];
    }

    /**
     * Get back trace list
     *
     * @param integer $slice Trace slice count OPTIONAL
     *
     * @return array
     */
    protected static function getBackTrace($slice = 2)
    {
        return \XLite\Core\Operator::getInstance()->getBackTrace($slice);
    }

    /**
     * Prepare back trace
     *
     * @param array $trace Back trace raw data
     *
     * @return array
     */
    protected function prepareBackTrace(array $trace)
    {
        return \XLite\Core\Operator::getInstance()->prepareBackTrace($trace);
    }

    /**
     * Detect class name by object
     *
     * @param object $obj Object
     *
     * @return string
     */
    protected function detectClassName($obj)
    {
        return is_object($obj) ? get_class($obj) : strval($obj);
    }

    /**
     * Convert PHP error code to PEAR error code
     *
     * @param integer $errno PHP error code
     *
     * @return integer
     */
    protected function convertPHPErrorToLogError($errno)
    {
        if (!isset($this->errorsTranslate)) {

            $this->errorsTranslate = array(
                E_ERROR             => LOG_ERR,
                E_WARNING           => LOG_WARNING,
                E_PARSE             => LOG_CRIT,
                E_NOTICE            => LOG_NOTICE,
                E_CORE_ERROR        => LOG_ERR,
                E_CORE_WARNING      => LOG_WARNING,
                E_COMPILE_ERROR     => LOG_ERR,
                E_COMPILE_WARNING   => LOG_WARNING,
                E_USER_ERROR        => LOG_ERR,
                E_USER_WARNING      => LOG_WARNING,
                E_USER_NOTICE       => LOG_NOTICE,
                E_STRICT            => LOG_NOTICE,
                E_RECOVERABLE_ERROR => LOG_ERR,
            );

            if (defined('E_DEPRECATED')) {
                $this->errorsTranslate[E_DEPRECATED] = LOG_WARNING;
                $this->errorsTranslate[E_USER_DEPRECATED] = LOG_WARNING;
            }
        }

        return isset($this->errorsTranslate[$errno]) ? $this->errorsTranslate[$errno] : LOG_INFO;
    }

    /**
     * Get PHP error name
     *
     * @param integer $errno PHP error code
     *
     * @return string
     */
    protected function getPHPErrorName($errno)
    {
        if (!isset($this->errorTypes)) {
            $this->errorTypes = array(
                E_ERROR             => 'Error',
                E_WARNING           => 'Warning',
                E_PARSE             => 'Parsing Error',
                E_NOTICE            => 'Notice',
                E_CORE_ERROR        => 'Error',
                E_CORE_WARNING      => 'Warning',
                E_COMPILE_ERROR     => 'Error',
                E_COMPILE_WARNING   => 'Warning',
                E_USER_ERROR        => 'Error',
                E_USER_WARNING      => 'Warning',
                E_USER_NOTICE       => 'Notice',
                E_STRICT            => 'Runtime Notice',
                E_RECOVERABLE_ERROR => 'Catchable fatal error',
            );

            if (defined('E_DEPRECATED')) {
                $this->errorTypes[E_DEPRECATED] = 'Warning (deprecated)';
                $this->errorTypes[E_USER_DEPRECATED] = 'Warning (deprecated)';
            }
        }

        return isset($this->errorTypes[$errno]) ? $this->errorTypes[$errno] : 'Unknown Error';
    }

    /**
     * Get rrror log path
     *
     * @return string
     */
    protected function getErrorLogPath()
    {
        return LC_DIR_LOG . 'php_errors.log.' . date('Y-m-d') . '.php';
    }
}
