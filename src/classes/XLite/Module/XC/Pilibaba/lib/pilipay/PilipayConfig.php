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
 * The configurations of Pilipay
 */
class PilipayConfig
{
    // Whether use HTTPS
    // 是否使用HTTPS
    private static $useHttps = true;

    // Whether it use production env.
    // 是否是生产环境
    private static $useProductionEnv = true;

    // The domain of pilibaba
    // 霹雳爸爸的域名
    const PILIBABA_DOMAIN_PRODUCTION = 'www.pilibaba.com';
    const PILIBABA_DOMAIN_TEST = 'pre.pilibaba.com';

    // The interface PATH for submit order
    // 提交订单的接口地址
    const SUBMIT_ORDER_PATH = '/pilipay/payreq';

    // The interface PATH for update tracking number
    // 更新运单号的接口地址
    const UPDATE_TRACK_NO_PATH = '/pilipay/updateTrackNo';

    // The interface PATH for barcode
    // 二维码的接口地址
    const BARCODE_PATH = '/pilipay/barCode';

    // The interface PATH for get warehouse address list
    // 中转仓地址列表的接口地址
    const WAREHOUSE_ADDRESS_PATH = '/pilipay/getAddressList';

    // The interface PATH for get supported currencies
    // 所支持的货币的接口地址
    const SUPPORTED_CURRENCIES_PATH = '/pilipay/getCurrency';

    /**
     * Check whether the configuration and PHP environment is OK
     * 检查配置和PHP环境是否OK
     * @param $errors array if all is OK, a empty array is returned. otherwise return a list of error message.
     * @return bool whether OK
     */
    public static function check(&$errors){
        $errors = array();

        if (!extension_loaded('curl') && !function_exists('fsockopen')){
            $errors[] = 'Curl extension or fsockopen is required';
        }

        if (self::useHttps() && !extension_loaded('openssl')){
            $errors[] = 'Openssl extension is required if you use HTTPS';
        }

        return empty($errors);
    }

    /**
     * Get whether to use HTTPS
     * 获取是否使用HTTPS
     * @return bool
     */
    public static function useHttps(){
        return self::$useHttps;
    }

    /**
     * Set whether to use HTTPS
     * 设置是否使用HTTPS
     * @param bool|true $useHttps
     */
    public static function setUseHttps($useHttps=true){
        self::$useHttps = $useHttps;
    }

    /**
     * Get whether to use production env.
     * 获取是否使用生产环境
     * @return bool
     */
    public static function useProductionEnv(){
        return self::$useProductionEnv;
    }

    /**
     * Set whether to use production env.
     * 设置是否使用生产环境
     * @param bool|true $isProduction
     */
    public static function setUseProductionEnv($isProduction=true){
        self::$useProductionEnv = $isProduction;
    }

    /**
     * The host (including the protocol) of pilibaba
     * @return string
     */
    public static function getPilibabaHost(){
        if (self::$useProductionEnv){
            return (self::$useHttps ? 'https' : 'http') . '://' . self::PILIBABA_DOMAIN_PRODUCTION;
        } else {
            return 'http://' . self::PILIBABA_DOMAIN_TEST;
        }
    }

    /**
     * The interface URL for submit order
     * @return string
     */
    public static function getSubmitOrderUrl(){
        return self::getPilibabaHost() . self::SUBMIT_ORDER_PATH;
    }

    /**
     * The interface URL for updating tracking number
     * @return string
     */
    public static function getUpdateTrackNoUrl(){
        return self::getPilibabaHost() . self::UPDATE_TRACK_NO_PATH;
    }

    /**
     * The interface URL for barcode
     * @return string
     */
    public static function getBarcodeUrl(){
        return self::getPilibabaHost() . self::BARCODE_PATH;
    }

    /**
     * The interface path for warehouse address list
     * @return string
     */
    public static function getWarehouseAddressListUrl(){
        return self::getPilibabaHost() . self::WAREHOUSE_ADDRESS_PATH;
    }

    /**
     * The interface URL for get supported currencies
     * @return string
     */
    public static function getSupportedCurrenciesUrl(){
        return self::getPilibabaHost() . self::SUPPORTED_CURRENCIES_PATH;
    }
}

