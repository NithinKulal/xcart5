<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Pilibaba\Logic;

/**
 * PilipayWarehouseAddressConverter
 */
class PilipayWarehouseAddressConverter
{
    /**
     * Convert address to Entity
     *
     * Example of address:
     *    "id" : 1,
     *    "country" : "USA",
     *     "firstName" : "JXPNC",
     *     "lastName" : "Pilibaba",
     *     "address" : "70 McCullough Dr #Z0367",
     *     "city" : "New Castle",
     *     "state" : "DE (DELAWARE )",
     *     "zipcode" : "19720",
     *     "tel" : "302-604-5010",
     *     "countryCode" : "USA",
     *     "iso2CountryCode" : "US",
     *     "isoStateCode" : "DE"
     *
     * @param  \PilipayWarehouseAddress $address Address
     * @param  \XLite\Model\Address     $initialAddressEntity Initial address OPTIONAL
     *
     * @return \XLite\Model\AEntity
     */
    public static function convertAddress(\PilipayWarehouseAddress $address, \XLite\Model\Address $initialAddressEntity = null)
    {
        $addressEntity = $initialAddressEntity ?: new \XLite\Model\Address();

        $fields = array(
            'firstName'     => 'firstName',
            'lastName'      => 'lastName',
            'countryCode'   => 'iso2CountryCode',
            'stateId'       => 'isoStateCode',
            'city'          => 'city',
            'zipcode'       => 'zipcode',
            'phone'         => 'tel',
        );

        $data = array();
        foreach ($fields as $ourFieldName => $fieldName) {
            $data[$ourFieldName] = $address->{$fieldName};
        }

        $countryObject = \XLite\Core\Database::getRepo('\XLite\Model\Country')->find($data['countryCode']);

        if ($countryObject) {
            $data['countryCode'] = $countryObject->getCode();
        } else {
            $data['countryCode'] = 'US';
        }

        $addressEntity->map($data);

        return $addressEntity;
    }
}
