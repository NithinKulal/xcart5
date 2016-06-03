<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CanadaPost\Model\Order;

/**
 * Class represents an Canada Post post office whice was selected for order
 *
 * @Entity
 * @Table  (name="order_capost_office")
 */
class PostOffice extends \XLite\Model\AEntity
{
    /**
     * Unique ID
     *
     * @var integer
     *
     * @Id
     * @GeneratedValue (strategy="AUTO")
     * @Column         (type="integer", options={ "unsigned": true })
     */
    protected $id;

    /**
     * Reference to the order model
     *
     * @var \XLite\Model\Order
     *
     * @OneToOne   (targetEntity="XLite\Model\Order", inversedBy="capostOffice")
     * @JoinColumn (name="orderId", referencedColumnName="order_id", onDelete="CASCADE")
     */
    protected $order;

    /**
     * The internal Canada Post assigned unique ID for a Post Office
     * (Field pattern: "\d{10}", has leading zeros)
     *
     * @var string
     *
     * @Column (type="string", options={ "fixed": true }, length=10)
     */
    protected $officeId;

    /**
     * The name assigned to the Post Office
     * (Max length: 40)
     *
     * @var string
     *
     * @Column (type="string", length=40)
     */
    protected $name;

    /**
     * The location of a Post Office. This is used to distinguish among various Post Offices that have similar names.
     * (Max length: 40)
     *
     * @var string
     *
     * @Column (type="string", length=40)
     */
    protected $location;

    /**
     * The distance (in KM) to the Post Office from the location specified in the query
     * (min: 0, max: 99999.99, fraction: 2)
     *
     * @var float
     *
     * @Column (type="decimal", precision=12, scale=2)
     */
    protected $distance = 0.00;

    /**
     * True indicates that the Post Office provides bilingual services (English and French)
     *
     * @var boolean
     *
     * @Column (type="boolean")
     */
    protected $bilingualDesignation = false;

    /**
     * Municipality in which the Post Office is located
     * (Max length: 40)
     *
     * @var string
     *
     * @Column (type="string", length=40)
     */
    protected $city;

    /**
     * The latitude of the Post Office
     * (min: 40, max: 90, fraction: 5)
     *
     * @var float
     *
     * @Column (type="decimal", precision=15, scale=5)
     */
    protected $latitude;

    /**
     * The longitude of the Post Office
     * (min: -150, max: -50, fraction: 5)
     * 
     * @var float
     *
     * @Column (type="decimal", precision=15, scale=5)
     */
    protected $longitude;

    /**
     * The Postal Code of the Post Office
     *
     * @var string
     *
     * @Column (type="string", length=20)
     */
    protected $postalCode;

    /**
     * The province where the Post Office is located
     *
     * @var string
     *
     * @Column (type="string", length=64)
     */
    protected $province;

    /**
     * Street number and name for a Post Office
     * (Max length: 64)
     *
     * @var string
     *
     * @Column (type="string", length=64)
     */
    protected $officeAddress;

    /**
     * Working hours list
     *
     * @var array
     *
     * @Column (type="array")
     */
    protected $workingHours;

    // {{{ Service methods
    
    /**
     * Set order
     *
     * @param \XLite\Model\Order $order Order object (OPTIONAL)
     *
     * @return void
     */

/*     
    public function setOrder(\XLite\Model\Order $order = null)
    {
        $this->order = $order;
    }
*/

    // }}

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set officeId
     *
     * @param string $officeId
     * @return PostOffice
     */
    public function setOfficeId($officeId)
    {
        $this->officeId = $officeId;
        return $this;
    }

    /**
     * Get officeId
     *
     * @return string 
     */
    public function getOfficeId()
    {
        return $this->officeId;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return PostOffice
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set location
     *
     * @param string $location
     * @return PostOffice
     */
    public function setLocation($location)
    {
        $this->location = $location;
        return $this;
    }

    /**
     * Get location
     *
     * @return string 
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Set distance
     *
     * @param decimal $distance
     * @return PostOffice
     */
    public function setDistance($distance)
    {
        $this->distance = $distance;
        return $this;
    }

    /**
     * Get distance
     *
     * @return decimal 
     */
    public function getDistance()
    {
        return $this->distance;
    }

    /**
     * Set bilingualDesignation
     *
     * @param boolean $bilingualDesignation
     * @return PostOffice
     */
    public function setBilingualDesignation($bilingualDesignation)
    {
        $this->bilingualDesignation = $bilingualDesignation;
        return $this;
    }

    /**
     * Get bilingualDesignation
     *
     * @return boolean 
     */
    public function getBilingualDesignation()
    {
        return $this->bilingualDesignation;
    }

    /**
     * Set city
     *
     * @param string $city
     * @return PostOffice
     */
    public function setCity($city)
    {
        $this->city = $city;
        return $this;
    }

    /**
     * Get city
     *
     * @return string 
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set latitude
     *
     * @param decimal $latitude
     * @return PostOffice
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;
        return $this;
    }

    /**
     * Get latitude
     *
     * @return decimal 
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * Set longitude
     *
     * @param decimal $longitude
     * @return PostOffice
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;
        return $this;
    }

    /**
     * Get longitude
     *
     * @return decimal 
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * Set postalCode
     *
     * @param string $postalCode
     * @return PostOffice
     */
    public function setPostalCode($postalCode)
    {
        $this->postalCode = $postalCode;
        return $this;
    }

    /**
     * Get postalCode
     *
     * @return string 
     */
    public function getPostalCode()
    {
        return $this->postalCode;
    }

    /**
     * Set province
     *
     * @param string $province
     * @return PostOffice
     */
    public function setProvince($province)
    {
        $this->province = $province;
        return $this;
    }

    /**
     * Get province
     *
     * @return string 
     */
    public function getProvince()
    {
        return $this->province;
    }

    /**
     * Set officeAddress
     *
     * @param string $officeAddress
     * @return PostOffice
     */
    public function setOfficeAddress($officeAddress)
    {
        $this->officeAddress = $officeAddress;
        return $this;
    }

    /**
     * Get officeAddress
     *
     * @return string 
     */
    public function getOfficeAddress()
    {
        return $this->officeAddress;
    }

    /**
     * Set workingHours
     *
     * @param array $workingHours
     * @return PostOffice
     */
    public function setWorkingHours($workingHours)
    {
        $this->workingHours = $workingHours;
        return $this;
    }

    /**
     * Get workingHours
     *
     * @return array 
     */
    public function getWorkingHours()
    {
        return $this->workingHours;
    }

    /**
     * Set order
     *
     * @param \XLite\Model\Order $order
     * @return PostOffice
     */
    public function setOrder(\XLite\Model\Order $order = null)
    {
        $this->order = $order;
        return $this;
    }

    /**
     * Get order
     *
     * @return \XLite\Model\Order 
     */
    public function getOrder()
    {
        return $this->order;
    }
}
