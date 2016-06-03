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
 * Class PilipayPayResult
 * This class helps to deal the callback payment result.
 * Note: directly `new` operation is not supported. You should always use `PilipayPayResult::fromRequest()` to create an instance.
 *
 * For example:
 *
 *     // create an instance from the request
 *     $payResult = PilipayPayResult::fromRequest();
 *
 *     // verify whether the request is valid:
 *     if (!$payResult->verify($appSecret)){ // $appSecret is exactly the same with $order->appSecret
 *         // error handling...
 *         die('Invalid request');
 *     }
 *
 *     // judge whether payment is successfully completed:
 *     if (!$payResult->isSuccess()){
 *         // deal failure
 *     } else {
 *         // deal success
 *     }
 *
 *
 * @property $merchantNo    string  the merchant number.
 * @property $orderNo       string  the order number. It's been passed to pilibaba via PilipayOrder.
 * @property $orderAmount   number  the total amount of the order. Its unit is the currencyType in the submitted PilipayOrder.
 * @property $signType      string  "MD5"
 * @property $signMsg       string  it's used for verify the request. Please use `PilipayPayResult::verify()` to verify it.
 * @property $orderTime      string  the time when the order was sent. Its format is like "2011-12-13 14:15:16".
 * @property $fee           number  the fee for Pilibaba
 * @property $customerMail  string  the customer's email address.
 * @property $errorCode     string  used for recording errors. If you want to check whether the payment is successfully completed, please use `isSuccess()` instead
 * @property $errorMsg      string  used for recording errors. If you want to check whether the payment is successfully completed, please use `isSuccess()` instead
 * @property $dealId        string  the transaction ID in Pilibaba.
 */
class PilipayPayResult
{
    protected $_merchantNo;
    protected $_orderNo;
    protected $_orderAmount;
    protected $_signType;
    protected $_signMsg;
    protected $_fee;
    protected $_orderTime;
    protected $_customerMail;
    protected $_errorCode;
    protected $_errorMessage;
    protected $_dealId;

    /**
     * @param array $request
     * @return PilipayPayResult
     */
    public static function fromRequest($request = null)
    {
        return new PilipayPayResult($request ? $request : $_REQUEST);
    }

    protected function __construct($request)
    {
        if (!empty($request)) {
            foreach ($request as $field => $value) {
                $field = '_' . $field;
                $this->{$field} = $value;
            }
        }
    }

    /**
     * @param $appSecret
     * @param bool $throws whether throws exception when fails
     * @return bool whether is valid request
     * @throws PilipayError
     */
    public function verify($appSecret, $throws = false)
    {
        $calcedSignMsg = md5($this->_merchantNo . $this->_orderNo . $this->_orderAmount
                            . $this->_signType . $this->_dealId . $this->_fee
                            . $this->_orderTime . $this->_customerMail . $appSecret);

        if (strcasecmp($calcedSignMsg, $this->_signMsg) !== 0){
            PilipayLogger::instance()->log("error", "Invalid signMsg: " . $this->_signMsg . " with secret: " . $appSecret . " with data: " . json_encode(get_object_vars($this)));

            if ($throws) {
                throw new PilipayError(PilipayError::INVALID_SIGN, $this->_signMsg);
            }

            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    public function isSuccess()
    {
        return true; // currently, if there is a callback request, it means the payment is successfully completed.
    }

    /**
     * @param $name
     * @return mixed
     * @throws PilipayError
     */
    public function __get($name)
    {
        $getter = 'get' . $name;
        if (method_exists($this, $getter)) {
            return $this->{$getter}();
        } else {
            throw new PilipayError(PilipayError::PROPERTY_NOT_EXIST, array($name));
        }
    }

    // setter using the default

    /**
     * return result to pilibaba
     * @param $result "1" or "OK" means result is success
     * @param $andDie bool
     * @return null
     */
    public function returnDealResultToPilibaba($result, $andDie=true){
        if ($result == 1 or $result == 'OK'){
            echo 'OK';
        } else {
            echo $result;
        }

        if ($andDie){
            die;
        }

        return null;
    }

    /**
     * @return mixed
     * @property $errorCode     string  used for recording errors. If you want to check whether the payment is successfully completed, please use `isSuccess()` instead
     */
    public function getErrorCode()
    {
        return $this->_errorCode;
    }

    /**
     * @return mixed
     * @property $errorMsg      string  used for recording errors. If you want to check whether the payment is successfully completed, please use `isSuccess()` instead
     */
    public function getErrorMsg()
    {
        return $this->_errorMessage;
    }

    /**
     * @return mixed
     * @property $merchantNo    string  the merchant number.
     */
    public function getMerchantNo()
    {
        return $this->_merchantNo;
    }

    /**
     * @return mixed
     * @property $orderNo       string  the order number. It's been passed to pilibaba via PilipayOrder.
     */
    public function getOrderNo()
    {
        return $this->_orderNo;
    }

    /**
     * @return mixed
     * @property $orderAmount   number  the total amount of the order. Its unit is the currencyType in the submitted PilipayOrder.
     */
    public function getOrderAmount()
    {
        return $this->_orderAmount / 100; // divide it by 100 -- as it's in cents over the HTTP API.
    }

    /**
     * @return mixed
     * @property $signType      string  "MD5"
     */
    public function getSignType()
    {
        return $this->_signType;
    }

    /**
     * @return mixed
     * @property $signMsg       string  it's used for verify the request. Please use `PilipayPayResult::verify()` to verify it.
     */
    public function getSignMsg()
    {
        return $this->_signMsg;
    }

    /**
     * @return mixed
     * @property $orderTime      string  the time when the order was sent. Its format is like "2011-12-13 14:15:16".
     */
    public function getOrderTime()
    {
        return $this->_orderTime;
    }

    /**
     * @return mixed
     * @property $dealId        string  the transaction ID in Pilibaba.
     */
    public function getDealId()
    {
        return $this->_dealId;
    }

    /**
     * @return mixed
     * @property $fee           number  the fee for Pilibaba
     */
    public function getFee()
    {
        return $this->_fee;
    }

    /**
     * @return mixed
     * @property $customerMail  string  the customer's email address.
     */
    public function getCustomerMail()
    {
        return $this->_customerMail;
    }
}

