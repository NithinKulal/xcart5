<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Geolocation\Model\Geolocation;

use XLite\Module\XC\Geolocation\lib\MaxMind;
use XLite\Module\XC\Geolocation\Logic;

/**
 * MaxMind geolocation provider
 */
class MaxMindGeoIP extends AProvider
{
    public function __construct()
    {
        $this->includeLibrary();
    }

    /**
     * Returns geolocation data in raw format (defined by provider)
     *
     * @param Logic\IGeoInput $data
     *
     * @return \GeoIp2\Record
     */
    public function getRawLocation(Logic\IGeoInput $data)
    {
        if (!($data instanceof Logic\GeoInput\IpAddress)) {
            return null;
        }

        try {
            $reader = $this->getReader();
            $record = $reader->country($data->getData());
        } catch (\Exception $e) {
            $record = null;
        }

        return $record;
    }

    /**
     * Returns human readable provider name.
     *
     * @return string
     */
    public function getProviderName()
    {
        return 'MaxMind GeoIP2';
    }

    /**
     * Returns list of accepted geo input types.
     *
     * @return array
     */
    public function acceptedInput()
    {
        return array('IpAddress');
    }

    /**
     * Transforms raw geolocation data to XCart format (array of address fields)
     *
     * @param mixed $data
     *
     * @return array
     */
    protected function transformData($data)
    {
        return array(
            'country' => $data->country->isoCode
        );
    }

    /**
     * Returns MaxMind reader object with loaded database.
     *
     * @return \GeoIp2\Database\Reader
     */
    protected function getReader()
    {
        return new \GeoIp2\Database\Reader($this->getGeoDb());
    }

    /**
     * Returns GeoLite2-Country db path.
     *
     * @return string
     */
    protected function getGeoDb()
    {
        return LC_DIR_MODULES . 'XC' . LC_DS . 'Geolocation' . LC_DS . 'lib' . LC_DS . 'MaxMind' . LC_DS . 'GeoLite2-Country.mmdb';
    }

    /**
     * Loads MaxMind php reader
     */
    protected function includeLibrary()
    {
        require_once LC_DIR_MODULES . 'XC' . LC_DS . 'Geolocation' . LC_DS . 'lib' . LC_DS . 'MaxMind' . LC_DS . 'geoip2.phar';
    }

}