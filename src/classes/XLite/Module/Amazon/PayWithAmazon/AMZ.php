<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\Amazon\PayWithAmazon;

/**
 * Amazon helper class
 */
class AMZ
{
    const AMAZON_PA_DEBUG        = false;
    const AMAZON_PA_PLATFORM_ID  = 'A1PQFSSKP8TT2U';
    const AMAZON_PA_HOST_PATTERN = '/^sns\.[a-zA-Z0-9\-]{3,}\.amazonaws\.com(\.cn)?$/';

    protected $jsUrls = [
        'test' => [
            'EUR' => 'https://static-eu.payments-amazon.com/OffAmazonPayments/de/sandbox/js/Widgets.js',
            'GBR' => 'https://static-eu.payments-amazon.com/OffAmazonPayments/uk/sandbox/js/Widgets.js',
            'USD' => 'https://static-na.payments-amazon.com/OffAmazonPayments/us/sandbox/js/Widgets.js',
        ],
        'live' => [
            'EUR' => 'https://static-eu.payments-amazon.com/OffAmazonPayments/de/js/Widgets.js',
            'GBR' => 'https://static-eu.payments-amazon.com/OffAmazonPayments/uk/js/Widgets.js',
            'USD' => 'https://static-na.payments-amazon.com/OffAmazonPayments/us/js/Widgets.js',
        ],
    ];

    /**
     * @var \XLite\Core\CommonCell
     */
    protected $config;

    /**
     * @param \XLite\Core\CommonCell $config
     */
    public function __construct($config)
    {
        $config->amazon_pa_region = in_array($config->amazon_pa_currency, ['EUR', 'GBR'], true)
            ? 'EU'
            : 'NA';

        $this->config = $config;
    }

    /**
     * @return \XLite\Core\CommonCell
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @return boolean
     */
    public function isConfigured()
    {
        return $this->config->amazon_pa_sid && $this->config->amazon_pa_client_id
            && \XLite\Core\Config::getInstance()->Security->customer_security;
    }

    /**
     * @return string
     */
    public function getJsUrl()
    {
        $mode = $this->config->amazon_pa_mode === 'live' ? 'live' : 'test';
        $currency = in_array($this->config->amazon_pa_currency, ['EUR', 'GBR'], true)
            ? $this->config->amazon_pa_currency
            : 'USD';
        $sid = $this->config->amazon_pa_sid;

        return $this->jsUrls[$mode][$currency] . '?sellerId=' . $sid;
    }

    /**
     * @param string $accessToken
     *
     * @return boolean
     */
    public function checkAccessToken($accessToken)
    {
        $result = false;

        $endPoint = 'https://api.amazon.com/auth/o2/tokeninfo?access_token=' . $accessToken;
        $request = new \XLite\Core\HTTP\Request($endPoint);

        $response = $request->sendRequest();

        if ($response && $response->body) {
            $data = json_decode($response->body, true);

            $result = isset($data['aud']) && $data['aud'] === $this->config->amazon_pa_client_id;
        }

        return $result;
    }

    /**
     * @param string $accessToken
     *
     * @return array|null
     */
    public function getProfileInfo($accessToken)
    {
        $request = new \XLite\Core\HTTP\Request('https://api.amazon.com/user/profile');
        $request->setHeader('Authorization', 'bearer ' . $accessToken);

        $response = $request->sendRequest();

        return $response && $response->body
            ? json_decode($response->body, true)
            : null;
    }

    public static function func_amazon_pa_debug($message, $xml = false)
    {

        if (!self::AMAZON_PA_DEBUG || empty($message))
            return true;

        if ($xml) {
            $message = self::func_xml_format($message);
        }

        \XLite\Logger::logCustom('amazon_pa', $message);

        return true;
    }

    public static function func_amazon_pa_error($message)
    {

        \XLite\Logger::logCustom('amazon_pa', $message);

        return true;
    }

    public static function _func_xml_make_tree($vals, &$i)
    {
        $children = [];

        if (isset($vals[$i]['value'])) {
            array_push($children, $vals[$i]['value']);
        }

        while (++$i < count($vals)) {
            switch ($vals[$i]['type']) {
                case 'open':
                    if (isset($vals[$i]['tag'])) {
                        $tagname = $vals[$i]['tag'];
                    } else {
                        $tagname = '';
                    }

                    if (isset($children[$tagname])) {
                        $size = sizeof($children[$tagname]);
                    } else {
                        $size = 0;
                    }

                    if (isset($vals[$i]['attributes'])) {
                        $children[$tagname][$size]['@'] = $vals[$i]["attributes"];
                    }

                    $children[$tagname][$size]['#'] = self::_func_xml_make_tree($vals, $i);
                    break;

                case 'cdata':
                    array_push($children, $vals[$i]['value']);
                    break;

                case 'complete':
                    $tagname = $vals[$i]['tag'];

                    if (isset($children[$tagname])) {
                        $size = sizeof($children[$tagname]);
                    } else {
                        $size = 0;
                    }

                    if (isset($vals[$i]['value'])) {
                        $children[$tagname][$size]["#"] = $vals[$i]['value'];
                    } else {
                        $children[$tagname][$size]["#"] = '';
                    }

                    if (isset($vals[$i]['attributes'])) {
                        $children[$tagname][$size]['@'] = $vals[$i]['attributes'];
                    }

                    break;

                case 'close':
                    return $children;
                    break;
            }
        }

        return $children;
    }

    public static function func_change_order_status($orderid, $orderStatus, $advinfo = false)
    {

        self::func_amazon_pa_debug("change order $orderid status to $orderStatus");

        $cart = \XLite\Core\Database::getRepo('XLite\Model\Order')->find($orderid);
        if ($cart) {
            \XLite\Model\Cart::setObject($cart);
        } else {
            self::func_amazon_pa_error("Cant find order $orderid to change its status");

            return;
        }

        $cart->setPaymentStatus($orderStatus);

        $orefid = $cart->getDetail('AmazonOrderReferenceId')->getValue();
        if (!empty($orefid)) {

            if ($orderStatus == \XLite\Model\Order\Status\Payment::STATUS_DECLINED || $orderStatus == \XLite\Model\Order\Status\Payment::STATUS_CANCELED) {
                // cancel ORO if declined
                self::func_amazon_pa_request('CancelOrderReference', [
                    'AmazonOrderReferenceId' => $orefid,
                ]);
            }

            if ($orderStatus == \XLite\Model\Order\Status\Payment::STATUS_PAID) {
                // close ORO
                self::func_amazon_pa_request('CloseOrderReference', [
                    'AmazonOrderReferenceId' => $orefid,
                ]);
            }
        }

        \XLite\Core\Database::getEM()->flush();
    }

    public static function func_amazon_pa_save_order_extra($orderid, $key, $val)
    {
        $cart = \XLite\Core\Database::getRepo('XLite\Model\Order')->find($orderid);

        if ($cart) {
            \XLite\Model\Cart::setObject($cart);
        } else {
            self::func_amazon_pa_debug("Cant find order $orderid to save extra data");

            return;
        }

        $cart->setDetail($key, $val);

        \XLite\Core\Database::getEM()->flush();
    }

    public static function func_xml_format($xml)
    {
        return \XLite\Core\XML::getFormattedXML($xml);
    }

    public static function func_xml_parse($data, &$error, $options = [])
    {

        static $default_options = [
            'XML_OPTION_CASE_FOLDING' => 0,
            'XML_OPTION_SKIP_WHITE'   => 1,
        ];

        $data = trim($data);
        $vals = $index = $array = [];
        $parser = xml_parser_create();
        $options = array_merge($default_options, $options);

        foreach ($options as $opt => $val) {
            if (!defined($opt))
                continue;

            xml_parser_set_option($parser, constant($opt), $val);
        }

        if (!xml_parse_into_struct($parser, $data, $vals, $index)) {
            $error = [
                'code'   => xml_get_error_code($parser),
                'string' => xml_error_string(xml_get_error_code($parser)),
                'line'   => xml_get_current_line_number($parser),
            ];
            xml_parser_free($parser);

            return false;
        }

        xml_parser_free($parser);

        $i = 0;

        $tagname = $vals[$i]['tag'];
        if (isset($vals[$i]['attributes'])) {
            $array[$tagname]['@'] = $vals[$i]['attributes'];
        } else {
            $array[$tagname]['@'] = [];
        }

        $array[$tagname]["#"] = self::_func_xml_make_tree($vals, $i);

        return $array;
    }


    public static function func_amazon_pa_request($action, $data)
    {
        if (\XLite\Core\Config::getInstance()->Amazon->PayWithAmazon->amazon_pa_currency === 'USD') {
            $urlHost = 'mws.amazonservices.com';
        } else {
            $urlHost = 'mws-eu.amazonservices.com';
        }
        if (\XLite\Core\Config::getInstance()->Amazon->PayWithAmazon->amazon_pa_mode === 'test') {
            $urlUri = '/OffAmazonPayments_Sandbox/2013-01-01';
        } else {
            $urlUri = '/OffAmazonPayments/2013-01-01';
        }

        $params = [
            'AWSAccessKeyId'   => \XLite\Core\Config::getInstance()->Amazon->PayWithAmazon->amazon_pa_access_key,
            'Action'           => $action,
            'SellerId'         => \XLite\Core\Config::getInstance()->Amazon->PayWithAmazon->amazon_pa_sid,
            'SignatureMethod'  => 'HmacSHA256',
            'SignatureVersion' => '2',
            'Timestamp'        => date('c'),
        ];

        // foreach ($data as $k => $v) {
        //     $params[] = "$k=$v";
        // }
        $params = array_merge($params, $data);

        ksort($params, \SORT_STRING);

        // sign request
        $concatParams = http_build_query($params, null, '&', \PHP_QUERY_RFC3986);
        $str2sign = "POST\n$urlHost\n$urlUri\n" . $concatParams;
        $signature = self::func_amazon_pa_sign($str2sign);
        $concatParams .= '&Signature=' . urlencode($signature);

        // send request
        self::func_amazon_pa_debug("send request=$urlHost$urlUri?$concatParams");
        $request = new \XLite\Core\HTTP\Request("https://$urlHost$urlUri");
        $request->body = $concatParams;
        $request->verb = 'POST';

        $response = $request->sendRequest();

        if (200 == $response->code && !empty($response->body)) {
            $reply = $response->body;
        } else {
            self::func_amazon_pa_error("Empty or wrong response received. Reply code=" . $response->code . " response body=" . $response->body);

            return false;
        }
        self::func_amazon_pa_debug($reply, true);

        $parse_error = [];
        $res = self::func_xml_parse($reply, $parse_error);
        if (!$res) {
            self::func_amazon_pa_error("Can not parse XML reply: " . print_r($parse_error, true));
        }

        return $res;
    }

    public static function func_amazon_pa_sign($data)
    {

        return base64_encode(hash_hmac('sha256', $data, \XLite\Core\Config::getInstance()->Amazon->PayWithAmazon->amazon_pa_secret_key, true));

    }

    public static function func_amazon_pa_ipn_verify_singature($message)
    {

        $signature = base64_decode($message['Signature']);
        $certificatePath = $message['SigningCertURL'];

        $parsed = parse_url($certificatePath);
        if (
            empty($parsed['scheme'])
            || empty($parsed['host'])
            || $parsed['scheme'] !== 'https'
            || substr($certificatePath, -4) !== '.pem'
            || !preg_match(self::AMAZON_PA_HOST_PATTERN, $parsed['host'])
        ) {
            self::func_amazon_pa_error('The certificate is located on an invalid domain.');

            return false;
        }

        $fields = [
            "Timestamp" => true,
            "Message"   => true,
            "MessageId" => true,
            "Subject"   => false,
            "TopicArn"  => true,
            "Type"      => true,
        ];

        ksort($fields);

        $signatureFields = [];
        foreach ($fields as $fieldName => $mandatoryField) {
            $value = $message[$fieldName];
            if (!is_null($value)) {
                array_push($signatureFields, $fieldName);
                array_push($signatureFields, $value);
            }
        }

        // create the signature string - key / value in byte order
        // delimited by newline character + ending with a new line character
        $data = implode("\n", $signatureFields) . "\n";

        $cert = file_get_contents($certificatePath);
        if (empty($cert)) {
            return false;
        }

        $certKey = openssl_get_publickey($cert);

        if ($certKey === false) {
            return false;
        }

        $result = openssl_verify($data, $signature, $certKey, OPENSSL_ALGO_SHA1);

        return ($result > 0);
    }

    public static function & func_array_path(&$array, $tag_path, $strict = false)
    {
        $not_found = false;
        if (!is_array($array) || empty($array)) {
            return $not_found;
        }

        if (empty($tag_path)) {
            return $array;
        }

        $path = explode('/', $tag_path);

        $elem =& $array;

        foreach ($path as $key) {
            if (isset($elem[$key])) {
                $tmp_elem =& $elem[$key];
            } else {
                if (!$strict && isset($elem['#'][$key])) {
                    $tmp_elem =& $elem['#'][$key];
                } else if (!$strict && isset($elem[0]['#'][$key])) {
                    $tmp_elem =& $elem[0]['#'][$key];
                } else {
                    // path is not found
                    return $not_found;
                }
            }

            unset($elem);
            $elem = $tmp_elem;
            unset($tmp_elem);
        }

        return $elem;
    }


}
