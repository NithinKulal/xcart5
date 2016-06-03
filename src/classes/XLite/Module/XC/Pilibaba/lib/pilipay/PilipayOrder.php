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
 * Class PilipayOrder
 *
 * required:
 * @property $version      string  API version.
 * @property $merchantNo   string  merchant number in account info page after signed up in pilibaba.com
 * @property $currencyType string  USD/EUR/GBP/AUD/CAD/JPY...
 * @property $orderNo      string  order number in your site, which identifies an order
 * @property $orderAmount  number  total order amount in currencyType
 * @property $orderTime    string  the time when the order was created, in format of 2001-12-13 14:15:16
 * @property $pageUrl      string  the order's checkout page
 * @property $serverUrl    string  the return URL after payment is completed successfully
 * @property $redirectUrl  string  pay success return page to user
 * @property $notifyType   string  what type of code I return. The value: html, json.
 * @property $shipper      number  ship fee (it's to houseware's fee, not the international ship fee) (in currencyType)
 * @property $tax          number  sales tax (in currencyType)
 *
 * @property $signType     string  "MD5" (fixed)
 * @property $signMsg      string  the sign messaged. it will be autometically calcuated
 *
 * @property $appSecret    string  app secret key in account info page
 *
 * as to goods -- you should use addGood() to add goods to the order
 *
 */
class PilipayOrder extends PilipayModel
{
    // The interface URL for barcode
    // 二维码的接口地址
    const BARCODE_URL = 'https://www.pilibaba.com/pilipay/barCode';

    private $_goodsList = array();

    public function __construct($properties=array()){
        $this->version = 'V2.0.01';
        $this->signType = 'MD5';
        $this->notifyType = 'html';

        parent::__construct($properties);
    }

    /**
     * @return array order data in API form
     * @throws PilipayError
     */
    public function toApiArray(){
        // check goods list
        if (empty($this->_goodsList)){
            throw new PilipayError(PilipayError::REQUIRED_ARGUMENT_NO_EXIST, array('name' => 'goodsList', 'value' => json_encode($this->_goodsList)));
        }

        // if the orderTime or sendTime is omitted, use current time
        if (empty($this->orderTime) or empty($this->sendTime)){
            $now = date_create('now', timezone_open('Asia/Shanghai'))->format('Y-m-d H:i:s');
            $this->orderTime = $this->orderTime ? $this->orderTime : $now;
            $this->sendTime = $this->sendTime ? $this->sendTime : $now;
        }

        // verify
        parent::verifyFields();

        $apiArray = array_map('strval', array(
            'version' => $this->version,
            'merchantNo' => $this->merchantNo,
            'currencyType' => $this->currencyType,
            'orderNo' => $this->orderNo,
            'orderAmount' => intval(round($this->orderAmount * 100)), // API: need to be in cent
            'orderTime' => $this->orderTime,
            'pageUrl' => $this->pageUrl,
            'serverUrl' => $this->serverUrl,
            'backUrl' => $this->serverUrl,
            'async' => 'false',
            'redirectUrl' => $this->redirectUrl,
            'notifyType' => $this->notifyType,
            'shipper' => intval(round($this->shipper * 100)), // API: need to be in cent
            'tax' => intval(round($this->tax * 100)), // API: need to be in cent
            'signType' => $this->signType,
        ));

        // sign
        if ($this->signType == 'MD5'){
            // sign using MD5
            $this->signMsg = md5(implode('', $apiArray) . $this->appSecret);
            $apiArray['signMsg'] = $this->signMsg;
        } else {
            throw new PilipayError(PilipayError::INVALID_ARGUMENT, array('name' => 'signType', 'value' => $this->signType));
        }

        $apiArray['goodsList'] = urlencode(json_encode($this->_goodsList));

        return $apiArray;
    }

    /**
     * Submit the order
     * 提交订单
     * @return array
     * @throws PilipayError
     */
    public function submit(){
        $this->notifyType = 'html';
        $orderData = $this->toApiArray();

        PilipayLogger::instance()->log('info', 'Submit order begin: '.json_encode($orderData));

        // submit
        $curl = new PilipayCurl();
        $curl->post(PilipayConfig::getSubmitOrderUrl(), $orderData);
        $responseStatusCode = $curl->getResponseStatusCode();
        $nextUrl = $curl->getResponseRedirectUrl();

        PilipayLogger::instance()->log('info', 'Submit order end: '. print_r(array(
                'url' => PilipayConfig::getSubmitOrderUrl(),
                'request' => $orderData,
                'response' => array(
                    'statusCode' => $curl->getResponseStatusCode(),
                    'statusText' => $curl->getResponseStatusText(),
                    'nextUrl' => $nextUrl,
                    'content' => $curl->getResponseContent(),
                )
            ), true));

        return array(
            'success' => $responseStatusCode < 400 && !empty($nextUrl),
            'errorCode' => $responseStatusCode,
            'message' => $curl->getResponseContent(),
            'nextUrl' => $nextUrl
        );
    }

    /**
     * Submit the order (x-cart patched version)
     * 提交订单
     * @return array
     * @throws PilipayError
     */
    public function submitPatched(){
        $orderData = $this->toApiArray();
        $this->notifyType = 'html';
        PilipayLogger::instance()->log('info', 'Submit order begin: ' . json_encode($orderData));

        // submit
        $curl = new PilipayCurl();
        $curl->post(PilipayConfig::getSubmitOrderUrl(), $orderData);

        $responseData = json_decode($curl->getResponseContent());
        $responseStatusCode = $responseData->code;
        $nextUrl            = $responseData->nextUrl;
        $message            = $responseData->message;
        PilipayLogger::instance()->log('info', 'Submit order end: '. print_r(array(
                'url' => PilipayConfig::getSubmitOrderUrl(),
                'request' => $orderData,
                'response' => array(
                    'statusCode' => $curl->getResponseStatusCode(),
                    'statusText' => $curl->getResponseStatusText(),
                    'nextUrl' => $nextUrl,
                    'content' => $curl->getResponseContent(),
                )
            ), true));

        return array(
            'success'       => $responseStatusCode < 400 && !empty($nextUrl),
            'statusCode'    => $responseStatusCode,
            'message'       => $message,
            'nextUrl'       => $nextUrl
        );
    }

    /**
     * Render a HTML form which will be autometically submitted via JavaScript.
     * @param string $method
     * @return string
     */
    public function renderSubmitForm($method="POST"){
        $action = PilipayConfig::getSubmitOrderUrl();

        $this->notifyType = 'html';
        $orderData = $this->toApiArray();

        PilipayLogger::instance()->log('info', "Submit order (using {$method} form): ".json_encode($orderData));

        $fields = '';
        foreach ($orderData as $name => $value) {
            $fields .= sprintf('<input type="hidden" name="%s" value="%s" />', $name, htmlspecialchars($value));
        }

        $html = <<<HTML_CODE
<form id="pilipaysubmit" name="pilipaysubmit" action="{$action}" method="{$method}" >
    {$fields}
    <input type="submit" value="submit" style="display: none;" />
</form>
<script type="text/javascript">
    document.forms['pilipaysubmit'].submit();
</script>
HTML_CODE;

        return $html;
    }

    /**
     * Update track number (logistics number)
     * @param $logisticsNo string the logistics tracking number
     * @throws PilipayError
     */
    public function updateTrackNo($logisticsNo){
        if (!$this->merchantNo){
            throw new PilipayError(PilipayError::REQUIRED_ARGUMENT_NO_EXIST, array('name' => 'merchantNo', 'value' => $this->merchantNo));
        }

        if (!$this->appSecret){
            throw new PilipayError(PilipayError::REQUIRED_ARGUMENT_NO_EXIST, array('name' => 'appSecret', 'value' => $this->appSecret));
        }

        if (!$this->orderNo){
            throw new PilipayError(PilipayError::REQUIRED_ARGUMENT_NO_EXIST, array('name' => 'orderNo', 'value' => $this->orderNo));
        }

        if (!$logisticsNo){
            throw new PilipayError(PilipayError::REQUIRED_ARGUMENT_NO_EXIST, array('name' => 'logisticsNo', 'value' => $logisticsNo));
        }

        $params = array(
            'orderNo' => $this->orderNo,
            'logisticsNo' => $logisticsNo,
            'merchantNo' => $this->merchantNo,
            'signMsg' => md5($this->orderNo . $logisticsNo . $this->merchantNo . $this->appSecret),
        );

        PilipayLogger::instance()->log('info', "Update track NO: ".json_encode($params));

        $curl = new PilipayCurl();
        $response = $curl->post(PilipayConfig::getUpdateTrackNoUrl(), $params);
        PilipayLogger::instance()->log('info', 'Update track NO result: '. print_r(array(
                'request' => $params,
                'response' => array(
                    'statusCode' => $curl->getResponseStatusCode(),
                    'statusText' => $curl->getResponseStatusText(),
                    'content' => $curl->getResponseContent()
                )
            ), true));

        if (!$response){
            throw new PilipayError(PilipayError::EMPTY_RESPONSE, 'Updating tacking number');
        }

        if (strcasecmp(trim($response), 'success') !== 0){
            throw new PilipayError(PilipayError::UPDATE_FAILED, 'Update tracking number failed: '.$response);
        }
    }

    /**
     * 添加商品信息
     * Add goods info
     * @param PilipayGood $good 商品信息
     */
    public function addGood(PilipayGood $good){
        $this->_goodsList[] = $good->toApiArray();
    }

    /**
     * Get the barcode's Picture URL
     * -- this barcode should be print on the cover of package before shipping, so that our warehouse could easily match the package.
     * 获取条形码的图片URL
     * -- 在邮寄前, 这个条形码应该打印到包裹的包装上, 以便我们的中转仓库识别包裹.
     * @return string the barcode's Picture URL
     */
    public function getBarcodePicUrl(){
        if (!$this->merchantNo){
            throw new PilipayError(PilipayError::REQUIRED_ARGUMENT_NO_EXIST, array('name' => 'merchantNo', 'value' => $this->merchantNo));
        }

        if (!$this->orderNo){
            throw new PilipayError(PilipayError::REQUIRED_ARGUMENT_NO_EXIST, array('name' => 'orderNo', 'value' => $this->orderNo));
        }

        return PilipayConfig::getBarcodeUrl() . '?' . http_build_query(array(
            'merchantNo' => $this->merchantNo,
            'orderNo' => $this->orderNo,
        ));
    }

    public function getNumericFieldNames(){
        return array('orderAmount', 'shipper', 'tax');
    }

    public function getRequiredFieldNames(){
        return array('version', 'merchantNo', 'appSecret', 'currencyType', 'orderNo', 'orderAmount',
                     'orderTime', 'pageUrl', 'serverUrl', 'redirectUrl', 'notifyType', 'shipper', 'tax', 'signType');
    }

}

