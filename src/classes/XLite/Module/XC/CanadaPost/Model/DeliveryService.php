<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
 
namespace XLite\Module\XC\CanadaPost\Model;

/**
 * Class represents a Canada Post delivery service
 *
 * @Entity
 * @Table  (name="capost_delivery_services",
 *      uniqueConstraints={
 *          @UniqueConstraint(name="code_country", columns={"code", "countryCode"})
 *      }
 * )
 * @HasLifecycleCallbacks
 */
class DeliveryService extends \XLite\Model\AEntity
{
    /**
     * Maximum time to live (in seconds)
     */
    const MAX_TTL = 259200; // 60 * 60 * 24 * 3 = 3 days

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
     * Service code
     *
     * @var string
     *
     * @Column (type="string", length=32, nullable=false)
     */
    protected $code;

    /**
     * Country
     *
     * @var string
     *
     * @Column (type="string", length=2)
     */
    protected $countryCode = '';

    /**
     * Service name
     *
     * @var string
     *
     * @Column (type="string", length=255, nullable=false)
     */
    protected $name;

    /**
     * Service expiration time (UNIX timestamp)
     *
     * @var integer
     *
     * @Column (type="integer", options={ "unsigned": true })
     */
    protected $expiry;

    /**
     * Maximum weight that can be sent using this service (in grams)
     *
     * @param integer
     *
     * @Column (type="integer", nullable=false)
     */
    protected $maxWeight = 0;

    /**
     * Minimum weight that can be sent using this service (in grams)
     *
     * @param integer
     *
     * @Column (type="integer", nullable=false)
     */
    protected $minWeight = 0;

    /**
     * Maximum size of the longest dimension of an item (in cm)
     *
     * @var float
     *
     * @Column (type="decimal", precision=11, scale=1)
     */
    protected $maxLength = 0.0;

    /**
     * Minimum size of the longest dimension of an item (in cm)
     *
     * @var float
     *
     * @Column (type="decimal", precision=11, scale=1)
     */
    protected $minLength = 0.0;

    /**
     * Maximum size of the second longest dimension of an item (in cm)
     *
     * @var float
     *
     * @Column (type="decimal", precision=11, scale=1)
     */
    protected $maxWidth = 0.0;

    /**
     * Minimum size of the second longest dimension of an item (in cm)
     *
     * @var float
     *
     * @Column (type="decimal", precision=11, scale=1)
     */
    protected $minWidth = 0.0;

    /**
     * Maximum size of the shortest dimension of an item (in cm)
     *
     * @var float
     *
     * @Column (type="decimal", precision=11, scale=1)
     */
    protected $maxHeight = 0.0;

    /**
     * Maximum size of the shortest dimension of an item (in cm)
     *
     * @var float
     *
     * @Column (type="decimal", precision=11, scale=1)
     */
    protected $minHeight = 0.0;

    /**
     * Maximum calculated value of length + 2*width + 2*height (in cm)
     *
     * @var float
     *
     * @Column (type="decimal", precision=11, scale=1, nullable=true)
     */
    protected $lengthPlusGirthMax;

    /**
     * Maximum value of length + width + height (in cm)
     *
     * @var float
     *
     * @Column (type="decimal", precision=11, scale=1, nullable=true)
     */
    protected $lengthHeightWidthSumMax;

    /**
     * If any dimension exceeds this limit an oversize fee will apply to the shipment (in cm)
     *
     * @var float
     *
     * @Column (type="decimal", precision=11, scale=1, nullable=true)
     */
    protected $oversizeLimit;

    /**
     * Standard density factor used to calculate cubed weight (in grams)
     *
     * @var integer
     *
     * @Column (type="integer", nullable=true)
     */
    protected $densityFactor;

    /**
     * True indicates that parcels shipped with this service can be shipped in a mailing tube (option CYL can be used)
     *
     * @var boolean
     *
     * @Column (type="boolean")
     */
    protected $canShipInMailingTube = false;

    /**
     * True indicates that parcels shipped with this service can be shipped unpackaged (option UP can be used)
     *
     * @var boolean
     *
     * @Column (type="boolean")
     */
    protected $canShipUnpackaged = false;

    /**
     * True indicates that this service can be used in the return-spec of a Create Shipment request
     *
     * @var boolean
     *
     * @Column (type="boolean")
     */
    protected $allowedAsReturnService = false;

    /**
     * Service options (reference to the service's options model)
     *
     * @var \Doctrine\Common\Collections\Collection
     *
     * @OneToMany (targetEntity="XLite\Module\XC\CanadaPost\Model\DeliveryService\Option", mappedBy="service", cascade={"all"})
     */
    protected $options;

    // {{{ Service methods

    /**
     * Add an option to service
     *
     * @param \XLite\Module\XC\CanadaPost\Model\DeliveryService\Option $newOption Service option model
     *
     * @return void
     */
    public function addOption(\XLite\Module\XC\CanadaPost\Model\DeliveryService\Option $newOption)
    {
        $newOption->setService($this);

        $this->addOptions($newOption);
    }

    // }}}

    /**
     * Check - is delivery service data is expired or not
     *
     * @return boolean
     */
    public function isExpired()
    {
        return (\XLite\Core\Converter::time() > $this->getExpiry());
    }

    /**
     * Update expiration time
     *
     * @return void
     */
    public function updateExpiry()
    {
        $this->setExpiry(\XLite\Core\Converter::time() + static::MAX_TTL);
    }

    // {{{ Lifecycle callbacks

    /**
     * Prepare before saving
     *
     * @PrePersist
     *
     * @return void
     */
    public function prepareBeforeSave()
    {
        if (
            !is_numeric($this->expiry)
            || !is_int($this->expiry)
        ) {
            $this->updateExpiry();
        }
    }

    // }}}

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
     * Set code
     *
     * @param string $code
     * @return DeliveryService
     */
    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }

    /**
     * Get code
     *
     * @return string 
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set countryCode
     *
     * @param string $countryCode
     * @return DeliveryService
     */
    public function setCountryCode($countryCode)
    {
        $this->countryCode = $countryCode;
        return $this;
    }

    /**
     * Get countryCode
     *
     * @return string 
     */
    public function getCountryCode()
    {
        return $this->countryCode;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return DeliveryService
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
     * Set expiry
     *
     * @param integer $expiry
     * @return DeliveryService
     */
    public function setExpiry($expiry)
    {
        $this->expiry = $expiry;
        return $this;
    }

    /**
     * Get expiry
     *
     * @return integer 
     */
    public function getExpiry()
    {
        return $this->expiry;
    }

    /**
     * Set maxWeight
     *
     * @param integer $maxWeight
     * @return DeliveryService
     */
    public function setMaxWeight($maxWeight)
    {
        $this->maxWeight = $maxWeight;
        return $this;
    }

    /**
     * Get maxWeight
     *
     * @return integer 
     */
    public function getMaxWeight()
    {
        return $this->maxWeight;
    }

    /**
     * Set minWeight
     *
     * @param integer $minWeight
     * @return DeliveryService
     */
    public function setMinWeight($minWeight)
    {
        $this->minWeight = $minWeight;
        return $this;
    }

    /**
     * Get minWeight
     *
     * @return integer 
     */
    public function getMinWeight()
    {
        return $this->minWeight;
    }

    /**
     * Set maxLength
     *
     * @param decimal $maxLength
     * @return DeliveryService
     */
    public function setMaxLength($maxLength)
    {
        $this->maxLength = $maxLength;
        return $this;
    }

    /**
     * Get maxLength
     *
     * @return decimal 
     */
    public function getMaxLength()
    {
        return $this->maxLength;
    }

    /**
     * Set minLength
     *
     * @param decimal $minLength
     * @return DeliveryService
     */
    public function setMinLength($minLength)
    {
        $this->minLength = $minLength;
        return $this;
    }

    /**
     * Get minLength
     *
     * @return decimal 
     */
    public function getMinLength()
    {
        return $this->minLength;
    }

    /**
     * Set maxWidth
     *
     * @param decimal $maxWidth
     * @return DeliveryService
     */
    public function setMaxWidth($maxWidth)
    {
        $this->maxWidth = $maxWidth;
        return $this;
    }

    /**
     * Get maxWidth
     *
     * @return decimal 
     */
    public function getMaxWidth()
    {
        return $this->maxWidth;
    }

    /**
     * Set minWidth
     *
     * @param decimal $minWidth
     * @return DeliveryService
     */
    public function setMinWidth($minWidth)
    {
        $this->minWidth = $minWidth;
        return $this;
    }

    /**
     * Get minWidth
     *
     * @return decimal 
     */
    public function getMinWidth()
    {
        return $this->minWidth;
    }

    /**
     * Set maxHeight
     *
     * @param decimal $maxHeight
     * @return DeliveryService
     */
    public function setMaxHeight($maxHeight)
    {
        $this->maxHeight = $maxHeight;
        return $this;
    }

    /**
     * Get maxHeight
     *
     * @return decimal 
     */
    public function getMaxHeight()
    {
        return $this->maxHeight;
    }

    /**
     * Set minHeight
     *
     * @param decimal $minHeight
     * @return DeliveryService
     */
    public function setMinHeight($minHeight)
    {
        $this->minHeight = $minHeight;
        return $this;
    }

    /**
     * Get minHeight
     *
     * @return decimal 
     */
    public function getMinHeight()
    {
        return $this->minHeight;
    }

    /**
     * Set lengthPlusGirthMax
     *
     * @param decimal $lengthPlusGirthMax
     * @return DeliveryService
     */
    public function setLengthPlusGirthMax($lengthPlusGirthMax)
    {
        $this->lengthPlusGirthMax = $lengthPlusGirthMax;
        return $this;
    }

    /**
     * Get lengthPlusGirthMax
     *
     * @return decimal 
     */
    public function getLengthPlusGirthMax()
    {
        return $this->lengthPlusGirthMax;
    }

    /**
     * Set lengthHeightWidthSumMax
     *
     * @param decimal $lengthHeightWidthSumMax
     * @return DeliveryService
     */
    public function setLengthHeightWidthSumMax($lengthHeightWidthSumMax)
    {
        $this->lengthHeightWidthSumMax = $lengthHeightWidthSumMax;
        return $this;
    }

    /**
     * Get lengthHeightWidthSumMax
     *
     * @return decimal 
     */
    public function getLengthHeightWidthSumMax()
    {
        return $this->lengthHeightWidthSumMax;
    }

    /**
     * Set oversizeLimit
     *
     * @param decimal $oversizeLimit
     * @return DeliveryService
     */
    public function setOversizeLimit($oversizeLimit)
    {
        $this->oversizeLimit = $oversizeLimit;
        return $this;
    }

    /**
     * Get oversizeLimit
     *
     * @return decimal 
     */
    public function getOversizeLimit()
    {
        return $this->oversizeLimit;
    }

    /**
     * Set densityFactor
     *
     * @param integer $densityFactor
     * @return DeliveryService
     */
    public function setDensityFactor($densityFactor)
    {
        $this->densityFactor = $densityFactor;
        return $this;
    }

    /**
     * Get densityFactor
     *
     * @return integer 
     */
    public function getDensityFactor()
    {
        return $this->densityFactor;
    }

    /**
     * Set canShipInMailingTube
     *
     * @param boolean $canShipInMailingTube
     * @return DeliveryService
     */
    public function setCanShipInMailingTube($canShipInMailingTube)
    {
        $this->canShipInMailingTube = $canShipInMailingTube;
        return $this;
    }

    /**
     * Get canShipInMailingTube
     *
     * @return boolean 
     */
    public function getCanShipInMailingTube()
    {
        return $this->canShipInMailingTube;
    }

    /**
     * Set canShipUnpackaged
     *
     * @param boolean $canShipUnpackaged
     * @return DeliveryService
     */
    public function setCanShipUnpackaged($canShipUnpackaged)
    {
        $this->canShipUnpackaged = $canShipUnpackaged;
        return $this;
    }

    /**
     * Get canShipUnpackaged
     *
     * @return boolean 
     */
    public function getCanShipUnpackaged()
    {
        return $this->canShipUnpackaged;
    }

    /**
     * Set allowedAsReturnService
     *
     * @param boolean $allowedAsReturnService
     * @return DeliveryService
     */
    public function setAllowedAsReturnService($allowedAsReturnService)
    {
        $this->allowedAsReturnService = $allowedAsReturnService;
        return $this;
    }

    /**
     * Get allowedAsReturnService
     *
     * @return boolean 
     */
    public function getAllowedAsReturnService()
    {
        return $this->allowedAsReturnService;
    }

    /**
     * Add options
     *
     * @param \XLite\Module\XC\CanadaPost\Model\DeliveryService\Option $options
     * @return DeliveryService
     */
    public function addOptions(\XLite\Module\XC\CanadaPost\Model\DeliveryService\Option $options)
    {
        $this->options[] = $options;
        return $this;
    }

    /**
     * Get options
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getOptions()
    {
        return $this->options;
    }
}
