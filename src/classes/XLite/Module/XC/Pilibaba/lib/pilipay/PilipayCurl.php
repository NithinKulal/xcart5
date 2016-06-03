<?php
/**
 * NOTICE OF LICENSE
 * Copyright (c) 2015~2016 Pilibaba.com
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 *
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 *
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.  IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 *
 *  @author    Pilibaba <developer@pilibaba.com>
 *  @copyright 2015~2016 Pilibaba.com
 *  @license   https://opensource.org/licenses/MIT The MIT License
 *  International Registered Trademark & Property of PrestaShop SA
 */

/**
 * Class PilipayCurl
 * This class provide an easier access to CURL.
 * 这个类使CURL用起来更方便.
 */
class PilipayCurl
{
    private $additionalHeaders;
    private $responseHeaders;
    private $responseContent;

    const CRLF = "\r\n";
    const CRLF_LEN = 2;
    const CRLFCRLF = "\r\n\r\n";

    /**
     * Nothing to do, just creat the object
     */
    public function __construct(){
    }

    /**
     * Set additional headers if you want to.
     * Normally it's not necessary
     * @param array $headers  in format: header key =>  header value
     */
    public function setAdditionalHeaders($headers){
        $this->additionalHeaders = $headers;
    }

    /**
     * Make a POST request
     * @param string $url               - the URL
     * @param array|string|null $params - if it's a string, it will passed as it is; if it's an array, http_build_query will be used to convert it to a string
     * @param int $timeout              - request timeout in seconds
     * @return string                   - the response content (without headers)
     */
    public function post($url, $params=null, $timeout=30){
        return $this->request('POST', $url, $params, $timeout);
    }

    /**
     * Make a GET request
     * @param string $url               - the URL
     * @param array|string|null $params - if it's a string, it will passed as it is; if it's an array, http_build_query will be used to convert it to a string
     * @param int $timeout              - request timeout in seconds
     * @return string                   - the response content (without headers)
     */
    public function get($url, $params=null, $timeout=30){
        return $this->request('GET', $url, $params, $timeout);
    }

    /**
     * Make a $method request
     * @param string $method            - GET/POST/...
     * @param string $url               - the URL
     * @param array|string|null $params - if it's a string, it will passed as it is; if it's an array, http_build_query will be used to convert it to a string
     * @param int $timeout              - request timeout in seconds
     * @return string|bool              - the response content (without headers) or false if failed
     */
    public function request($method, $url, $params=null, $timeout=30){
        if (extension_loaded('curl')){
            return $this->_requestViaCurl($method, $url, $params, $timeout);
        } else {
            return $this->_requestViaFsockopen($method, $url, $params, $timeout);
        }
    }

    protected function _requestViaCurl($method, $url, $params, $timeout){
        $options = array(
            CURLOPT_HTTPGET => false,
            CURLOPT_HEADER => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_MAXREDIRS => 0,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_USERAGENT => 'curl',
            CURLOPT_CONNECTTIMEOUT => $timeout,
            CURLOPT_TIMEOUT => $timeout
        );

        $additionalHeaders = $this->additionalHeaders;

        switch (strtoupper($method)){
            case 'GET':
                if (!empty($params)){
                    $url .= '?' . (is_array($params) ? http_build_query($params) : strval($params));
                }
                $ch = curl_init($url);
                $options[CURLOPT_HTTPGET] = true;
                break;
            default: // post...
                $ch = curl_init($url);
                $options[CURLOPT_CUSTOMREQUEST] = $method;
                if (!empty($params)){
                    $options[CURLOPT_POSTFIELDS] = (is_array($params) ? http_build_query($params) : strval($params));
                    $additionalHeaders['Content-Type'] = 'application/x-www-form-urlencoded; charset=utf-8';
                }
                break;
        }

        if (!$ch){
            throw new PilipayError(PilipayError::CURL_ERROR, 'failed to initialize CURL');
        }

        $headers = array();
        if (!empty($additionalHeaders)){
            foreach ($additionalHeaders as $key => $value){
                $headers[] = $key . ': ' . $value;
            }

            $options[CURLOPT_HTTPHEADER] = $headers;
        }

        foreach ($options as $optKey => $optVal) {
            curl_setopt($ch, $optKey, $optVal);
        }

        $response = curl_exec($ch);
        $curlInfo = curl_getinfo($ch);
        $errCode = curl_errno($ch);
        $errMsg = curl_error($ch);
        curl_close($ch);

        $headerSize = $curlInfo['header_size'];
        $responseHeader = substr($response, 0, $headerSize);
        $responseBody = substr($response, $headerSize);

        PilipayLogger::instance()->log('debug', "CURL: ".print_r(array(
                'request' => array(
                    'method' => $method,
                    'url' => $url,
                    'params' => $params,
                    'headers' => $headers
                ),
                'response' => array(
                    'errno' => $errCode,
                    'error' => $errMsg,
                    'header' => $responseHeader,
                    'body' => $responseBody,
                )
            ), true));

        if ($errCode != 0 && $errMsg){
            throw new PilipayError(PilipayError::CURL_ERROR, "{$errMsg} ($errCode)");
        }

        $this->responseHeaders = self::parseResponseHeader($responseHeader);
        $this->responseHeaders['redirect_url'] = $curlInfo['redirect_url'];
        $this->responseContent = $responseBody;
        return $this->responseContent;
    }

    protected function _requestViaFsockopen($method, $url, $params, $timeout){
        // prepare
        $additionalHeaders = array_merge(array(
            'Connection' => 'close',
            'User-Agent' => 'curl',
            'Accept' => 'text/html,text/*,*/*',
        ), (array)$this->additionalHeaders);
        $requestContent = '';
        $urlInfo = parse_url($url);

        switch ($method){
            case 'GET':
                if (!empty($params)){
                    $urlInfo['query'] = (!empty($urlInfo['query']) ? $urlInfo['query'] . '&' . http_build_query($params) : http_build_query($params));
                }
                break;
            default: // POST, DELETE...
                if (!empty($params)){
                    $requestContent = http_build_query($params);
                    $additionalHeaders['Content-Type'] = 'application/x-www-form-urlencoded; charset=utf-8';
                    $additionalHeaders['Content-Length'] = strlen($requestContent);
                }
                break;
        }

        // build headers of request
        $requestHeaders = array(
            strtr('{method} {pathAndQuery} HTTP/1.1', array(
                    '{method}' => strtoupper($method),
                    '{pathAndQuery}' => (!empty($urlInfo['path']) ? $urlInfo['path'] : '/')
                                      . (!empty($urlInfo['query']) ? '?' . $urlInfo['query'] : '')
                )),
            'Host: ' . $urlInfo['host'],
        );

        foreach ($additionalHeaders as $headerKey => $headerValue){
            $requestHeaders[] = "{$headerKey}: {$headerValue}";
        }

        $request = implode(self::CRLF, $requestHeaders) . self::CRLFCRLF . $requestContent;

        // open sock file
        if ($urlInfo['scheme'] == 'https'){
            $sockFileName = 'ssl://' . $urlInfo['host'];
            $sockPort = isset($urlInfo['port']) ? intval($urlInfo['port']) : 443;
        } else {
            $sockFileName = $urlInfo['host'];
            $sockPort = isset($urlInfo['port']) ? intval($urlInfo['port']) : 80;
        }

        $sockFile = fsockopen($sockFileName, $sockPort, $sockErrCode, $sockErrMsg, $timeout);
        if (!$sockFile){
            throw new PilipayError(PilipayError::CURL_ERROR, "failed to open sock file: {$sockErrMsg} ($sockErrCode)");
        }

        // write the request
        fwrite($sockFile, $request);

        // read the response
        $gotResponseHeaderEnd = false;
        $hasResponseContentLenInHeader = false;
        $responseBodyLen = false;
        $responseHeader = '';
        $responseBody = '';
        while (!feof($sockFile)) {
            $line = fgets($sockFile);
            if (!$gotResponseHeaderEnd){
                if ($line === self::CRLF){
                    $gotResponseHeaderEnd = true;
                    $this->responseHeaders = $this->parseResponseHeader($responseHeader);
                    if ($this->responseHeaders['Content-Length']){
                        $responseBodyLen = $this->responseHeaders['Content-Length'];
                        $hasResponseContentLenInHeader = true;
                    }
                } else {
                    $responseHeader .= $line;
                }
            } else {
                $responseBody .= $line;
            }
        }

        fclose($sockFile);

        if (!$hasResponseContentLenInHeader){
            // parse the response-body-len from hex token
            // then, cut down the response body
            $responseBody = $this->parseResponseBody($responseBody);
        }

        PilipayLogger::instance()->log('debug', "CURL: ".print_r(array(
                'request' => array(
                    'method' => $method,
                    'url' => $url,
                    'params' => $params,
                    'headers' => $requestHeaders,
                ),
                'response' => array(
                    'errno' => $sockErrCode,
                    'error' => $sockErrMsg,
                    'header' => $responseHeader,
                    'body' => $responseBody,
                )
            ), true));

        $this->responseContent = $responseBody;

        return $responseBody;
    }

    /**
     * parse the response headers, convert into key => value formatted array
     * @param string $headerText
     * @return array
     */
    public static function parseResponseHeader($headerText){
        $headers = array();

        foreach (explode("\n", $headerText) as $header) {
            if (preg_match('/^HTTP\/(?<version>\d+\.\d+)\s+(?<statusCode>\d+)\s+(?<statusText>.*)$/', $header, $matches)){
                $headers['version'] = $matches['version'];
                $headers['statusCode'] = $matches['statusCode'];
                $headers['statusText'] = $matches['statusText'];
                continue;
            }

            $delimeterPos = strpos($header, ':');
            if ($delimeterPos !== false){
                $key = trim(substr($header, 0, $delimeterPos));
                $headers[$key] = trim(substr($header, $delimeterPos + 1));
            } else {
                // ignore unknown headers...
            }
        }

        return $headers;
    }

    public static function parseResponseBody($rest){
        $responseBody = '';

        while (true){
            $crlfPos = strpos($rest, self::CRLF);
            if ($crlfPos === false){
                break;
            }

            sscanf(substr($rest, 0, $crlfPos), "%x", $responseBodyLen);
            $responseBody .= substr($rest, $crlfPos + self::CRLF_LEN, $responseBodyLen);
            $rest = substr($rest, $crlfPos + self::CRLF_LEN + $responseBodyLen + self::CRLF_LEN);
        }

        return $responseBody;
    }

    /**
     * @return string the response's status code, i.e: 200, 301, 400, 500...
     */
    public function getResponseStatusCode(){
        return $this->getResponseHeader('statusCode');
    }

    /**
     * @return string the response's status text, i.e: OK, Found...
     */
    public function getResponseStatusText(){
        return $this->getResponseHeader('statusText');
    }

    /**
     * @return string the URL for redirecting, normally when the status code is 30x
     */
    public function getResponseRedirectUrl(){
        $url = $this->getResponseHeader('redirect_url');
        if ($url){
            return $url;
        } else {
            return $this->getResponseHeader('Location');
        }
    }

    /**
     * @param string $key  - the header key
     * @return string|null - the header value
     */
    public function getResponseHeader($key){
        return $this->responseHeaders[$key];
    }

    /**
     * @return string|null - the response content (without headers)
     */
    public function getResponseContent(){
        return $this->responseContent;
    }

    /**
     * @return PilipayCurl
     */
    public static function instance(){
        static $instance = null;

        if (!$instance){
            $instance = new PilipayCurl();
        }

        return $instance;
    }
}

