<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MultiCurrency\Core\RateProvider;

/**
 * Cache decorator
 */
class WebserviceX extends \XLite\Module\XC\MultiCurrency\Core\RateProvider\ARateProvider
{
    /**
     * URL to post request for rate
     *
     * @var string
     */
    protected $url = 'http://www.webservicex.net/CurrencyConvertor.asmx/ConversionRate';

    /**
     * Get currency conversion rate
     *
     * @param string $from Source currency code (alpha-3)
     * @param string $to   Destination currency code (alpha-3)
     *
     * @return float
     */
    public function getRate($from, $to)
    {
        $result = null;

        $data = array(
            'FromCurrency' => $from,
            'ToCurrency'   => $to,
        );

        $postData = array();

        foreach ($data as $k => $v) {
            $postData[] = "$k=$v";
        }

        $request = new \XLite\Core\HTTP\Request($this->url . '?' . implode('&', $postData));

        $request->verb = 'GET';

        $response = $request->sendRequest();

        if (!empty($response->body)) {
            $rate = $this->parseResponse($response->body);

            if ($rate) {
                $result = doubleval($rate);
            }
        }

        return $result;
    }


    /**
     * Parse server response.
     * Expected format of response:
     * <?xml version="1.0" encoding="utf-8"?>
     * <double xmlns="http://www.webserviceX.NET/">30.8663</double>
     *
     * @param string $response Server response
     *
     * @return string
     */
    protected function parseResponse($response)
    {
        $result = null;

        if (preg_match('/<double.*>([\d.]+)<\/double>/', $response, $match)) {
            $result = $match[1];
        }

        return $result;
    }
}