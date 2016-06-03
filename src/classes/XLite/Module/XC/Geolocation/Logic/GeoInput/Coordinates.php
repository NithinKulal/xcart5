<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Geolocation\Logic\GeoInput;

/**
 * Latitude-longitude input type
 */
class Coordinates implements \XLite\Module\XC\Geolocation\Logic\IGeoInput
{
    /**
     * @var string latitude component
     */
    protected $latitude;

    /**
     * @var string longitude component
     */
    protected $longitude;

    public function __construct($latitude, $longitude)
    {
        $this->latitude = $latitude;
        $this->longitude = $longitude;
    }

    /**
     * Returns scalar representation of internal geo data.
     *
     * @return array
     */
    public function getData()
    {
        return array(
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
        );
    }

    /**
     * Returns hash of geo data, is used as key in cache.
     *
     * @return string
     */
    public function getHash()
    {
        return md5(serialize($this));
    }

    /**
     * Returns latitude string
     *
     * @return string
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * Returns longitude string
     *
     * @return string
     */
    public function getLongitude()
    {
        return $this->longitude;
    }
}
