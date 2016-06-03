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
class GoogleFinance extends \XLite\Module\XC\MultiCurrency\Core\RateProvider\ARateProvider
{
    /**
     * URL to post request for rate
     *
     * @var string
     */
    protected $url = 'http://www.google.com/finance/converter';

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
            'a'    => 1,
            'from' => $from,
            'to'   => $to,
        );

        $postData = array();

        foreach ($data as $k => $v) {
            $postData[] = "$k=$v";
        }

        $request = new \XLite\Core\HTTP\Request($this->url . '?' . implode('&', $postData));

        $request->verb = 'GET';

        $response = $request->sendRequest();

        if (!empty($response->body)) {
            $rate = $this->parseResponse($from, $to, $response->body);

            if ($rate) {
                $result = doubleval($rate);
            }
        }

        return $result;
    }


    /**
     * Parse server response
     *
     * @param string $from     Source currency code (alpha-3)
     * @param string $to       Destination currency code (alpha-3)
     * @param string $response Server response
     *
     * @return string
     */
    protected function parseResponse($from, $to, $response)
    {
        $result = null;

        $pattern = sprintf('1 %s =.*([\d.]+) %s', $from, $to);

        if (preg_match('/' . $pattern . '/SsU', $response, $match)) {
            $result = $match[1];
        }

        return $result;
    }
}