<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MailChimp\Logic\DataMapper;


class Address
{
    /**
     * @param \XLite\Model\Address $address
     *
     * @return array
     */
    public static function getData(\XLite\Model\Address $address)
    {
        $data = [
            'address1'      => $address->getStreet(),
            'city'          => $address->getCity(),
            'phone'         => $address->getPhone(),
            'postal_code'   => $address->getZipcode(),
        ];
        
        if ($address->getCountry()) {

            $data['country']        = $address->getCountry()->getCountry();
            $data['country_code']   = $address->getCountry()->getCode();

            if ($address->getState()) {
                $data['province']       = $address->getState()->getState();

                if ($address->getCountry()->hasStates()) {
                    $data['province_code']  = $address->getState()->getCode();
                }
            }
        }

        return $data;
    }

    /**
     * @param \XLite\Model\Address $address
     *
     * @return array
     */
    public static function getDataWithNames(\XLite\Model\Address $address)
    {  
        return [
            $address->getFirstname(),
            $address->getLastname(),
            static::getData($address)
        ];
    }
}