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
 * Class PilipayError
 * This class represents for errors in Pilipay.
 * For reducing the library's size, we use error code to distinguish different types of errors.
 */
class PilipayError extends Exception
{
    const INVALID_ARGUMENT = 411;
    const REQUIRED_ARGUMENT_NO_EXIST = 412;
    const INVALID_SIGN = 413;
    const PROPERTY_NOT_EXIST = 414;
    const INVALID_CURL_PARAMS_FORMAT = 511;
    const CURL_ERROR = 512;
    const EMPTY_RESPONSE = 513;
    const UPDATE_FAILED = 514;

    /**
     * @param int $errorCode
     * @param array|string $errorData
     * @param Exception|null $previous
     */
    public function __construct($errorCode, $errorData, $previous=null){
        $msg = $this->buildErrorMessage($errorCode, $errorData);
        parent::__construct($msg, $errorCode, $previous);
    }

    /**
     * @param int $errorCode
     * @param array|string $errorData
     * @return string
     */
    protected function buildErrorMessage($errorCode, $errorData){
        if (is_array($errorData)){
            $params = array();
            foreach ($errorData as $key => $val){
                $params['{' . $key .'}'] = $val;
            }
        } else {
            $params = array('{}' => $errorData, '{0}' => $errorData);
        }

        return strtr(self::$errorCodeToMessageMap[$errorCode], $params);
    }

    protected static $errorCodeToMessageMap = array(
        self::INVALID_ARGUMENT => 'Invalid {name}: {value}',
        self::REQUIRED_ARGUMENT_NO_EXIST => 'The required {name} is empty: {value}',
        self::INVALID_SIGN => 'Invalid sign: {}',
        self::PROPERTY_NOT_EXIST => 'Property not exist: {}',
        self::INVALID_CURL_PARAMS_FORMAT => 'Invalid CURL params\' format: {}',
        self::CURL_ERROR => 'CURL error: {}',
        self::EMPTY_RESPONSE => '{} got an empty response',
        self::UPDATE_FAILED => 'Update failed: {}',
    );
}

