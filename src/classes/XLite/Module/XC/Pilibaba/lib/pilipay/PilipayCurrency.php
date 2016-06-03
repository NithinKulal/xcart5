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
 * Class PilipayCurrency
 * This class helps to query all currencies Pilibaba supported.
 *
 * @property string $code   - the currency code of this currency (see ISO_4217)
 *
 */
class PilipayCurrency extends PilipayModel
{
    /**
     * query all available currencies from pilibaba
     * @param string $resultFormat objectList | stringList
     * @return array
     */
    public static function queryAll($resultFormat='objectList'){
        $curl = PilipayCurl::instance();
        $result = $curl->get(PilipayConfig::getSupportedCurrenciesUrl());
        if (empty($result)){
            return array();
        }

        $json = json_decode($result, true);
        if (empty($json)){
            return array();
        }

        if ($resultFormat !== 'objectList'){
            return $json;
        }

        $currencies = array();
        foreach ($json as $currencyCode) {
            $currencies[] = new PilipayCurrency(array(
                'code' => $currencyCode
            ));
        }

        return $currencies;
    }
}

