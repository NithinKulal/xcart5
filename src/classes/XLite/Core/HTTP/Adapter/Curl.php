<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\HTTP\Adapter;

/**
 * Custom Curl adapter for HTTP\Request
 */
class Curl extends \PEAR2\HTTP\Request\Adapter\Curl
{
    /**
     * The number of seconds to wait while trying to connect
     *
     * @var integer
     */
    protected $connectTimeout = 15;

    /**
     * Additional cURL options
     *
     * @var array
     */
    protected $additionalOptions = array();

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
        $this->additionalOptions[$name] = $value;
    }

    /**
     * Add curl options
     *
     * @return void
     */
    protected function _setupRequest()
    {
        parent::_setupRequest();

        // The number of seconds to wait while trying to connect
        curl_setopt($this->curl, \CURLOPT_CONNECTTIMEOUT, $this->connectTimeout);

        // The maximum number of seconds to allow cURL functions to execute
        curl_setopt($this->curl, \CURLOPT_TIMEOUT, $this->connectTimeout + $this->requestTimeout);

        if ($this->verb === 'HEAD') {
            curl_setopt($this->curl, CURLOPT_NOBODY, true);
        }

        if (preg_match('/^https/', $this->uri->url)) {

            if (\XLite\Core\Config::getInstance()->Environment->curl_cainfo) {
                curl_setopt($this->curl, \CURLOPT_CAINFO, \XLite\Core\Config::getInstance()->Environment->curl_cainfo);

            } elseif (\XLite\Core\Config::getInstance()->Environment->curl_capath) {
                curl_setopt($this->curl, \CURLOPT_CAPATH, \XLite\Core\Config::getInstance()->Environment->curl_capath);
            }
        }

        foreach ($this->additionalOptions as $name => $value) {
            curl_setopt($this->curl, $name, $value);
        }
    }

    /**
     * Send cURL request
     *
     * @return \PEAR2\HTTP\Request\Response
     * @throws \PEAR2\HTTP\Request\Exception
     */
    protected function _sendRequest()
    {
        $body = curl_exec($this->curl);
        $this->_notify('disconnect');

        if (false === $body) {
            \XLite\Core\Session::getInstance()->storeCURLError(curl_errno($this->curl));
            \XLite\Core\Session::getInstance()->storeCURLErrorMessage(curl_error($this->curl));
            throw new \PEAR2\HTTP\Request\Exception(
                'Curl ' . curl_error($this->curl) . ' (' . curl_errno($this->curl) . ')'
            );
        }

        $this->sentFilesize = false;

        if ($this->fp !== false) {
            fclose($this->fp);
        }

        $details = $this->uri->toArray();
        $details['code'] = curl_getinfo($this->curl, \CURLINFO_HTTP_CODE);

        $headers = new \PEAR2\HTTP\Request\Headers($this->headers);
        $cookies = array();

        return new \PEAR2\HTTP\Request\Response($details, $body, $headers, $cookies);
    }
}
