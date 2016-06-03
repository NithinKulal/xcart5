<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\HTTP;

/**
 * Request
 */
class Request extends \PEAR2\HTTP\Request
{
    /**
     * Error message
     *
     * @var string
     */
    protected $errorMsg = null;

    /**
     * Sets up the adapter
     *
     * @param string                      $url      URL for this request OPTIONAL
     * @param \PEAR2\HTTP\Request\Adapter $instance The adapter to use OPTIONAL
     *
     * @return void
     */
    public function __construct($url = null, $instance = null)
    {
        if (!$instance && extension_loaded('curl')) {
            $instance = new \XLite\Core\HTTP\Adapter\Curl;
        }

        try {
            parent::__construct($url, $instance);

        } catch (\Exception $exception) {
            $this->errorMsg = $exception->getMessage();
            $this->logBouncerError($exception);
        }
    }

    /**
     * Asks for a response class from the adapter
     *
     * @return \PEAR2\HTTP\Request\Response
     */
    public function sendRequest()
    {
        try {
            $result = parent::sendRequest();

        } catch (\Exception $exception) {
            $result = null;
            $this->errorMsg = $exception->getMessage();
            $this->logBouncerError($exception);
        }

        return $result;
    }

    /**
     * Sends a request storing the output to a file
     *
     * @param string $file File to store to
     *
     * @return \PEAR2\HTTP\Request\Response
     */
    public function requestToFile($file)
    {
        try {
            $result = parent::requestToFile($file);

        } catch (\Exception $exception) {
            $result = null;
            $this->errorMsg = $exception->getMessage();
            $this->logBouncerError($exception);
        }

        return $result;
    }

    /**
     * Get last error message
     *
     * @return string
     */
    public function getErrorMessage()
    {
        return $this->errorMsg;
    }

    /**
     * Set additional cURL option
     *
     * @param string $name  Option name
     * @param mixed  $value Option value
     *
     * @return void
     */
    public function setAdditionalOption($name, $value)
    {
        if ($this->adapter instanceof \XLite\Core\HTTP\Adapter\Curl) {
            $this->adapter->setAdditionalOption($name, $value);
        }
    }

    /**
     * Logging
     *
     * @param \Exception $exception Thrown exception
     *
     * @return void
     */
    protected function logBouncerError(\Exception $exception)
    {
        \XLite\Logger::getInstance()->log($exception->getMessage(), $this->getLogLevel());
    }

    /**
     * Return type of log messages
     *
     * @return integer
     */
    protected function getLogLevel()
    {
        return LOG_WARNING;
    }
}
