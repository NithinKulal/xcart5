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
 * Class PilipayWarehouseAddress
 * This class helps to query all warehouse addresses of pilibaba
 *
 * @property string $country        - the country of the warehouse
 * @property string $countryCode    - the country code of the warehouse @see https://en.wikipedia.org/wiki/ISO_3166-1
 * @property string $firstName      - the first name of the receiver
 * @property string $lastName       - the last name of the receiver
 * @property string $address        - the address of the warehouse
 * @property string $city           - the city name of the warehouse
 * @property string $state          - the state name of the warehosue
 * @property string $zipcode        - the zipcode/postcode of the warehouse
 * @property string $tel            - the telephone number of the receiver
 */
class PilipayWarehouseAddress extends PilipayModel
{
    /**
     * query all warehouse addresses
     * @params $resultFormat string objectList or arrayList
     * @return array
     */
    public static function queryAll($resultFormat='objectList'){
        $curl = PilipayCurl::instance();
        $result = $curl->get(PilipayConfig::getWarehouseAddressListUrl());
        if (empty($result)){
            return array();
        }

        $json = json_decode($result, true);
        if (empty($json)){
            return false;
        }

        if ($resultFormat !== 'objectList'){
            return $json;
        }

        $addressList = array();
        foreach($json as $item){
            $addressList[] = new PilipayWarehouseAddress($item);
        }

        return $addressList;
    }
}

