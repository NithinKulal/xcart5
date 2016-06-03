<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CanadaPost\Model;

/**
 * Class represents an Canada Post Post Office
 */
class PostOffice extends \XLite\Base\SuperClass
{
    /**
     * The internal Canada Post assigned unique identification number for the Post Office
     * (Field pattern: "\d{10}", has leading zeros)
     *
     * @var string
     */
    protected $id;

    /**
     * The name assigned to the Post Office
     * (Max length: 40)
     *
     * @var string
     */
    protected $name;

    /**
     * The location of a Post Office. This is used to distinguish among various Post Offices that have similar names.
     * (Max length: 40)
     *
     * @var string
     */
    protected $location;

    /**
     * The distance (in KM) to the Post Office from the location specified in the query
     * (min: 0, max: 99999.99, fraction: 2)
     *
     * @var float
     */
    protected $distance = 0.00;

    /**
     * True indicates that the Post Office provides bilingual services (English and French)
     *
     * @var boolean
     */
    protected $bilingualDesignation = false;

    /**
     * This element represents a link to the Get Post Office Detail web service
     *
     * @var string
     */
    protected $linkHref;

    /**
     * This element represents a link to the Get Post Office Detail web service
     *
     * @var string
     */
    protected $linkMediaType;

    /**
     * Municipality in which the Post Office is located
     * (Max length: 40)
     *
     * @var string
     */
    protected $city;

    /**
     * The latitude of the Post Office
     * (min: 40, max: 90, fraction: 5)
     *
     * @var float
     */
    protected $latitude;

    /**
     * The longitude of the Post Office
     * (min: -150, max: -50, fraction: 5)
     * 
     * @var float
     */
    protected $longitude;

    /**
     * The Postal Code of the Post Office
     *
     * @var string
     */
    protected $postalCode;

    /**
     * The province where the Post Office is located
     *
     * @var string
     */
    protected $province;

    /**
     * Street number and name for a Post Office
     * (Max length: 64)
     *
     * @var string
     */
    protected $officeAddress;

    /**
     * Public class constructor
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * Set post office ID
     *
     * @param string $value Post office ID
     *
     * @return void
     */
    public function setId($value)
    {
        $this->id = $value;
    }
    
    /**
     * Get post office ID
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }
    
    /** 
     * Set post office name
     *
     * @param string $value Post office name
     *
     * @return void
     */
    public function setName($value)
    {
        $this->name = $value;
    }
    
    /**
     * Get post office name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * Set location of a post office
     *
     * @param string $value Post office location
     *
     * @return void
     */
    public function setLocation($value)
    {
        $this->location = $value;
    }
    
    /**
     * Get location of a post office
     *
     * @return string
     */
    public function getLocation()
    {
        return $this->location;
    }
    
    /**
     * Set the distance (in KM) to a post office 
     *
     * @param float $value Distance
     *
     * @return void
     */
    public function setDistance($value)
    {
        $this->distance = $value;
    }
    
    /**
     * Get the distance (in KM) to a post office
     *
     * @return float
     */
    public function getDistance()
    {
        return $this->distance;
    }
    
    /**
     * Set bilingual service flag
     *
     * @param boolean $value Bilingual service flag
     *
     * @return void
     */
    public function setBilingualDesignation($value)
    {
        $this->bilingualDesignation = (bool) $value;
    }

    /**
     * Get bilingual service flag
     *
     * @return boolean
     */
    public function getBilingualDesignation()
    {
        return $this->bilingualDesignation;
    }

    /**
     * Set post office details link href
     *
     * @param string $value Link href
     *
     * @return void
     */
    public function setLinkHref($value)
    {
        $this->linkHref = $value;
    }
    
    /**
     * Get post office details link href
     *
     * @return string
     */
    public function getLinkHref()
    {
        return $this->linkHref;
    }
    
    /**
     * Set post office details link media type
     *
     * @param string $value Link media type
     *
     * @return void
     */
    public function setLinkMediaType($value)
    {
        $this->linkMediaType = $value;
    }
    
    /**
     * Get post office details link media type
     *
     * @return string
     */
    public function getLinkMediaType()
    {
        return $this->linkMediaType;
    }
    
    /**
     * Set post office city
     *
     * @param string $value City
     *
     * @return void
     */
    public function setCity($value)
    {
        $this->city = $value;
    }

    /**
     * Get post office city
     *
     * @return city
     */
    public function getCity()
    {
        return $this->city;
    }
    
    /**
     * Set the latitude of a post office
     *
     * @param float $value Latitude
     *
     * @return void
     */
    public function setLatitude($value)
    {
        $this->latitude = doubleval($value);
    }
    
    /**
     * Get the latitude of a post office
     *
     * @return float
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * Set the longitude of a post office
     *
     * @param float $value Longitude
     *
     * @return void
     */
    public function setLongitude($value)
    {
        $this->longitude = doubleval($value);
    }
    
    /**
     * Get the longitude of a post office
     *
     * @return float
     */
    public function getLongitude()
    {
        return $this->longitude;
    }
    
    /**
     * Set the postal code a the post office
     *
     * @param string $value Postal code
     *
     * @return void
     */
    public function setpostalCode($value)
    {
        $this->postalCode = $value;
    }
    
    /**
     * Get the postal code a the post office
     *
     * @return string
     */
    public function getpostalCode()
    {
        return $this->postalCode;
    }

    /**
     * Set the province where a post office is located
     *
     * @param string $value Province
     *
     * @return void
     */
    public function setProvince($value)
    {
        $this->province = $value;
    }
    
    /**
     * Get the province where a post office is located
     *
     * @return string
     */
    public function getProvince()
    {
        return $this->province;
    }
    
    /**
     * Set street number and name for a post office
     *
     * @param string $value Street number and name
     *
     * @return void
     */
    public function setOfficeAddress($value)
    {
        $this->officeAddress = $value;
    }
    
    /**
     * Get street number and name for a post office
     *
     * @return string
     */
    public function getOfficeAddress()
    {
        return $this->officeAddress;
    }
}
