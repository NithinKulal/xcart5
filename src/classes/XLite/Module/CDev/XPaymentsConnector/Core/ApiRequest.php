<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * X-Cart
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the software license agreement
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.x-cart.com/license-agreement.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to licensing@x-cart.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not modify this file if you wish to upgrade X-Cart to newer versions
 * in the future. If you wish to customize X-Cart for your needs please
 * refer to http://www.x-cart.com/ for more information.
 *
 * @category  X-Cart 5
 * @author    Qualiteam software Ltd <info@x-cart.com>
 * @copyright Copyright (c) 2011-2016 Qualiteam software Ltd <info@x-cart.com>. All rights reserved
 * @license   http://www.x-cart.com/license-agreement.html X-Cart 5 License Agreement
 * @link      http://www.x-cart.com/
 */

namespace XLite\Module\CDev\XPaymentsConnector\Core;

/**
 * X-Payments API request 
 */
class ApiRequest extends \XLite\Base
{
    /**
     * Salt block length
     */
    const XPC_SALT_LENGTH = 32;

    /**
     * Salt generator start character code
     */
    const XPC_SALT_BEGIN = 33;

    /**
     * Salt generator end character code
     */
    const XPC_SALT_END = 255;

    /**
     * Encryption check length
     */
    const XPC_CHUNK_LENGTH = 128;

    /**
     * Root-level tag for all XML messages
     */
    const XPC_TAG_ROOT = 'data';

    /**
     * Value of the 'type' attribute for list items in XML
     */
    const XPC_TYPE_CELL = 'cell';

    /**
     * Default charset
     */
    const DEFAULT_CHARSET = 'UTF-8';

    /**
     * Log file names
     */
    const LOG_FILE       = 'xp-connector';
    const LOG_FILE_ERROR = 'xp-connector-error';

    /**
     * Here stored response from X-Payments
     */
    protected $response = null;

    /**
     * Write error to log
     *
     * @param string $error Error message
     * @param mixed $logData Log data
     *
     * @return void
     */
    public static function writeLogError($error, $logData = null)
    {
        if (!is_null($logData)) {

            if (is_scalar($logData)) {
                $logData = strval($logData);
            } else {
                $logData = var_export($logData, true);
            }

            $logData = $error . PHP_EOL . $logData;

        } else {
           $logData = $error;
        }

        \XLite\Logger::getInstance()->logCustom(self::LOG_FILE_ERROR, $logData, true);
    }

    /**
     * Throw error and write log
     *
     * @param string $error Error message
     * @param mixed $logData Log data
     *
     * @return void
     */
    public static function throwError($error, $logData = null)
    {
        self::writeLogError($error, $logData);

        throw new \XLite\Module\CDev\XPaymentsConnector\Core\XpcResponseException($error);
    }

    /**
     * Make X-Payments API request
     *
     * @param string $target Request target
     * @param string $action Request action
     * @param array  $data   Request data OPTIONAL
     * @param string $apiVersion API version, overrides configuration value OPTIONAL
     *
     * @return array
     */
    public function send($target, $action, array $data = array(), $apiVersion = false)
    {
        $this->response->cleanup();

        if (!$apiVersion) {
            // Use API version from config if different one is not specified as parameter 
            $apiVersion = $this->getConfig()->xpc_api_version;
        }

        try {

            $client = \XLite\Module\CDev\XPaymentsConnector\Core\XPaymentsClient::getInstance();

            // Check configuration
            if (!$client->isModuleConfigured()) {
                self::throwError('Module is not configured');
            }
       
            // Check requirements 
            if (0 !== $client->checkRequirements()) {
                self::throwError('Check module requirements is failed');
            }

            $data['target'] = $target;
            $data['action'] = $action;

            // Send API version
            $data['api_version'] = $apiVersion;

            // Convert array to XML
            $xml = $this->convertHashToXml($data);

            if (!$xml) {
                self::throwError('Data is not valid');
            }

            // Encrypt
            $request = $this->encryptXml($xml);

            if (!$request) {
                self::throwError('Data is not encrypted');
            }

            // Send request to X-Payments
            $response = $this->sendApiRequest($request);

            // Check errors returned by X-Payments
            $this->checkError($response);

            // Response must be an array representation with some necessary data
            if (!$this->checkResponseContent($target, $action, $response)) {
                self::throwError('Response is not properly formatted or does not contain the necessary data', $response);
            }

            // Set successfull response
            $this->response->fill(true, $response);

        } catch (\XLite\Module\CDev\XPaymentsConnector\Core\XpcResponseException $exception) {

            // Set error response
            $this->response->fill(false, null, $exception->getMessage());

        }

        return $this->response;
    }

    /**
     * Check reponse content for action and target
     *
     * @param string $target Request target
     * @param string $action Request action
     * @param array  $reponse Decrypted and converted data from X-Payments
     *
     * @return bool 
     */
    protected function checkResponseContent($target, $action, $response)
    {
        $method = 'check' 
            . \XLite\Core\Converter::getInstance()->convertToCamelCase($target)
            . \XLite\Core\Converter::getInstance()->convertToCamelCase($action)
            . 'ResponseValid';

        return is_array($response) 
            && (
                method_exists($this, $method) 
                ? $this->$method($response)
                : true
            );
    }

    /**
     * Check reponse for initial payment request is valid
     *
     * @param array $response Response from X-Payments
     *
     * @return bool
     */
    protected static function checkPaymentInitResponseValid($response)
    {
        return !empty($response['token'])
            && is_string($response['token'])
            && !empty($response['txnId'])
            && is_string($response['txnId']);
    }

    /**
     * Check response for errors 
     *
     * @param array $response Formatted response from X-Payments
     *
     * @return void 
     */
    protected function checkError($response)
    {
        $message = $code = '';

        if (!empty($response['error_message'])) {
            $message = $response['error_message'];
        }

        if (!empty($response['error'])) {
            $code = $response['error'];
        }

        if ($code || $message) {

            $error = \XLite\Module\CDev\XPaymentsConnector\Core\XPaymentsClient::getInstance()
                ->composeErrorMessage($code, $message);

            self::throwError($error);
        }
    }

    /**
     * Encrypt data (RSA)
     *
     * @param string $data Request data
     *
     * @return string
     */
    public function encryptXml($data)
    {
        $result = false;

        $key = openssl_pkey_get_public($this->getConfig()->xpc_public_key);
        if (!$key) {
            // This is a public method, and thus we should check the key
            self::throwError('No public key');
        }

        // Preprocess
        srand(time());
        $salt = '';
        for ($i = 0; static::XPC_SALT_LENGTH > $i; $i++) {
            $salt .= chr(rand(static::XPC_SALT_BEGIN, static::XPC_SALT_END));
        }

        $lenSalt = strlen($salt);

        $crcType = 'MD5';
        $crc = md5($data, true);

        $crc = str_repeat(' ', 8 - strlen($crcType)) . $crcType . $crc;
        $lenCRC = strlen($crc);

        $lenData = strlen($data);

        $data = str_repeat('0', 12 - strlen((string)$lenSalt)) . $lenSalt . $salt
            . str_repeat('0', 12 - strlen((string)$lenCRC)) . $lenCRC . $crc
            . str_repeat('0', 12 - strlen((string)$lenData)) . $lenData . $data;

        $data = str_split($data, static::XPC_CHUNK_LENGTH);
        $crypttext = null;

        // Encrypt            
        foreach ($data as $k => $chunk) {
            if (!openssl_public_encrypt($chunk, $crypttext, $key)) {
                self::throwError('Encrypt chunk failed');
            }

            $data[$k] = $crypttext;
        }

        // Postprocess
        $data = array_map('base64_encode', $data);

        $result = 'API' . implode("\n", $data);

        return $result;
    }

    /**
     * Send API request
     *
     * @param string $request Encrypted XML
     *
     * @return array
     */
    protected function sendApiRequest($request)
    {
        // HTTPS request
        $post = array(
            'cart_id' => $this->getConfig()->xpc_shopping_cart_id,
            'request' => $request,
        );

        $this->getCurlHeadersCollector(false);
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->getConfig()->xpc_xpayments_url . '/api.php');
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15000);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt($ch, CURLOPT_HEADERFUNCTION, array($this, 'getCurlHeadersCollector'));

        if (!empty(\XLite\Core\Config::getInstance()->Security->https_proxy)) {
            // uncomment this line if you need proxy tunnel
            // curl_setopt ($ch, CURLOPT_HTTPPROXYTUNNEL, true);
            curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
            curl_setopt($ch, CURLOPT_PROXY, \XLite\Core\Config::getInstance()->Security->https_proxy);
        }

        // insecure key is supported by curl since version 7.10
        $version = curl_version();

        if (is_array($version)) {
            $version = 'libcurl/' . $version['version'];
        }

        if (preg_match('/libcurl\/([^ $]+)/Ss', $version, $m)) {
            $parts = explode('.', $m[1]);
            if (7 < $parts[0] || (7 == $parts[0] && 10 <= $parts[1])) {
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
            }
        }

        $body = curl_exec($ch);
        $errno = curl_errno($ch);
        $error = curl_error($ch);

        $headers = $this->getCurlHeadersCollector(true);

        curl_close($ch);

        // Check curl error
        if (!empty($error) || 0 != $errno) {
            self::throwError('Communication error', 'Curl error #' . $errno . ': ' . $error);
        }

        return $this->processApiResponse($body, $headers);
    }

    /**
     * Send API request
     *
     * @param string $body Response body
     * @param string $headers Response headers
     *
     * @return array
     */
    public function processApiResponse($body, $headers = '')
    {
        // Check raw data
        if (substr($body, 0, 3) !== 'API') {

            $logData = 'Response headers: ' . var_export($headers, true) . PHP_EOL
                . 'Response: ' . var_export($body, true);

            self::throwError('Response is not valid', $logData);
        }

        // Decrypt
        $response = $this->decryptXml($body);

        // Convert XML to array
        $response = $this->convertXmlToHash($response);
        if (!is_array($response)) {
            self::throwError('Unable to convert response into XML');
        }

        // The 'Data' tag must be set in response
        if (!isset($response[static::XPC_TAG_ROOT])) {
            self::throwError('Response does not contain any data');
        }
        
        return $response[static::XPC_TAG_ROOT];
    }

    /**
     * Decrypt (RSA)
     *
     * @param string $data Encrypted data
     *
     * @return array
     */
    protected function decryptXml($data)
    {
        $result = null;

        // Decrypt
        $res = openssl_get_privatekey($this->getConfig()->xpc_private_key, $this->getConfig()->xpc_private_key_password);
        
        if (!$res) {
            // This is a public method, and thus we should check the key
            self::throwError('Private key is not initialized');
        }
    
        $data = substr($data, 3);

        $data = explode("\n", $data);
        $data = array_map('base64_decode', $data);

        foreach ($data as $k => $s) {

            if (!openssl_private_decrypt($s, $newsource, $res)) {
                self::throwError('Can not decrypt chunk');
            }

            $data[$k] = $newsource;
        }

        openssl_free_key($res);

        // Postprocess
        $result = $this->decryptXmlPostprocess(implode('', $data));

        return $result;
    }

    /**
     * Decrypt XML postprocess
     *
     * @param string $data Decrypted data
     *
     * @return array
     */
    protected function decryptXmlPostprocess($data)
    {
        $lenSalt = substr($data, 0, 12);
        if (!preg_match('/^\d+$/Ss', $lenSalt)) {
            self::throwError('Salt length prefix has wrong format');
        }

        $lenSalt = intval($lenSalt);
        $data = substr($data, 12 + intval($lenSalt));

        $lenCRC = substr($data, 0, 12);
        if (!preg_match('/^\d+$/Ss', $lenCRC) || 9 > $lenCRC) {
            self::throwError('CRC length prefix has wrong format');
        } 

        $lenCRC = intval($lenCRC);
        $crcType = trim(substr($data, 12, 8));
        if ('MD5' !== $crcType) {
            $result = self::throwError('CRC hash is not MD5');
        }
                    
        $crc = substr($data, 20, $lenCRC - 8);

        $data = substr($data, 12 + $lenCRC);

        $lenData = substr($data, 0, 12);
        if (!preg_match('/^\d+$/Ss', $lenData)) {
            self::throwError('Data block length prefix has wrong format');
        }

        $data = substr($data, 12, intval($lenData));

        $currentCRC = md5($data, true);
        if ($currentCRC !== $crc) {
            self::throwError('Original CRC and calculated CRC is not equal');
        }

        return $data;
    }

    /**
     * Convert XML to hash array
     *
     * @param string $xml XML string
     *
     * @return array|string
     */
    public function convertXmlToHash($xml)
    {
        $data = array();

        while (
            !empty($xml)
            && preg_match('/<([\w\d]+)(?:\s*type=["\'](\w+)["\']\s*)?' . '>(.*)<\/\1>/Us', $xml, $matches)
        ) {

            // Sublevel tags or tag value
            if (static::XPC_TYPE_CELL === $matches[2]) {
                $data[$matches[1]][] = $this->convertXmlToHash($matches[3]);

            } else {
                $data[$matches[1]] = $this->convertXmlToHash($matches[3]);
            }

            // Exclude parsed part from XML
            $xml = str_replace($matches[0], '', $xml);
        }

        return empty($data) ? $xml : $data;
    }

    /**
     * Convert hash array to XML
     *
     * @param array   $data  Hash array
     * @param integer $level Current recursion level OPTIONAL
     *
     * @return string
     */
    public function convertHashToXml(array $data, $level = 0)
    {
        $xml = '';

        foreach ($data as $name => $value) {

            if ($this->isAnonymousArray($value)) {
                foreach ($value as $item) {
                    $xml .= $this->writeXmlTag($item, $name, $level, static::XPC_TYPE_CELL);
                }

            } else {
                $xml .= $this->writeXmlTag($value, $name, $level);
            }

        }

        return $xml;
    }

    /**
     * Check if passed variable is an array with numeric keys
     *
     * @param array $data Data to check
     *
     * @return boolean
     */
    protected function isAnonymousArray($data)
    {
        return is_array($data)
            && (1 > count(preg_grep('/^\d+$/', array_keys($data), PREG_GREP_INVERT)));
    }

    /**
     * Write XML tag for current level
     *
     * @param mixed   $data  Node content
     * @param string  $name  Node name
     * @param integer $level Current recursion level OPTIONAL
     * @param string  $type  Value for 'type' attribute OPTIONAL
     *
     * @return string
     */
    protected function writeXmlTag($data, $name, $level = 0, $type = '')
    {
        $xml = '';
        $indent = str_repeat('  ', $level);

        // Open tag
        $xml .= $indent . '<' . $name . (empty($type) ? '' : ' type="' . $type . '"') . '>';

        // Sublevel tags or tag value
        $xml .= is_array($data)
            ? "\n" . $this->convertHashToXml($data, $level + 1) . $indent
            : $this->convertLocalToUtf8($data);

        // Close tag
        $xml .= '</' . $name . '>' . "\n";

        return $xml;
    }

    /**
     * Convert local string ti UTF-8
     *
     * @param string $string  Request data
     * @param string $charset Charset OPTIONAL
     *
     * @return string
     */
    protected function convertLocalToUtf8($string, $charset = null)
    {
        if (is_null($charset)) {
            $charset = static::DEFAULT_CHARSET;
        }

        $charset = strtolower(trim($charset));

        if (function_exists('utf8_encode') && 'iso-8859-1' == $charset) {
            $string = utf8_encode($string);

        } elseif (function_exists('iconv')) {
            $string = iconv($charset, 'utf-8', $string);

        } else {

            $len = strlen($string);
            $data = '';
            for ($i = 0; $i < $len; $i++) {
                $c = ord(substr($string, $i, 1));
                if (!(22 > $c || 127 < $c)) {
                    $data .= substr($string, $i, 1);
                }
            }

            $string = $data;
        }

        return $string;
    }

    /**
     * CURL headers collector callback
     *
     * @return mixed
     */
    protected function getCurlHeadersCollector()
    {
        static $headers = '';

        $args = func_get_args();

        if (count($args) == 1) {

            $return = '';

            if ($args[0] == true) {
                $return = $headers;
            }

            $headers = '';

        } else {

            if (trim($args[1]) != '') {
                $headers .= $args[1];
            }
            $return = strlen($args[1]);
        }

        return $return;
    }

    /**
     * Get X-Payments Connector module configuration 
     *
     * @return object 
     */
    public function getConfig()
    {
        return \XLite\Core\Config::getInstance()->CDev->XPaymentsConnector;
    }

    /**
     * Constructor 
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->response = new \XLite\Module\CDev\XPaymentsConnector\Transport\Response;
    }
}
