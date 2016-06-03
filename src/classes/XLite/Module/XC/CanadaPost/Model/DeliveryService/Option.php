<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
 

namespace XLite\Module\XC\CanadaPost\Model\DeliveryService;

/**
 * Class represents a Canada Post delivery service's option
 *
 * @Entity
 * @Table  (name="capost_delivery_service_options")
 */
class Option extends \XLite\Model\AEntity
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
     * Option code
     *
     * @var string
     *
     * @Column (type="string", length=255, nullable=false)
     */
    protected $code;

    /**
     * Option name
     * TODO: remove that field and make getting an option name by a function
     *
     * @var string
     *
     * @Column (type="string", length=255, nullable=false)
     */
    protected $name;

    /**
     * Indicates whether this option is mandatory for the service
     *
     * @var boolean
     *
     * @Column (type="boolean")
     */
    protected $mandatory = false;

    /**
     * True indicates that this option if selected must include a qualifier on the option.
     * This is true for insurance (COV) and collect on delivery (COD) options
     *
     * @var boolean
     *
     * @Column (type="boolean")
     */
    protected $qualifierRequired = false;

    /**
     * Numeric – indicates the maximum value of the qualifier for this service.
     * The maximum value of a qualifier may differ between services. This is specific to the insurance (COV) option.
     *
     * @var float
     *
     * @Column (type="decimal", precision=14, scale=4, nullable=true)
     */
    protected $qualifierMax = 0.0000;

    /**
     * Item's service (reference to the item's service model)
     *
     * @var \XLite\Module\XC\CanadaPost\Model\DeliveryService
     *
     * @ManyToOne  (targetEntity="XLite\Module\XC\CanadaPost\Model\DeliveryService", inversedBy="options")
     * @JoinColumn (name="serviceId", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $service;

    // {{{ Service methods

    /**
     * Assign the service
     *
     * @param \XLite\Module\XC\CanadaPost\Model\DeliveryService $service Item's service model (OPTIONAL)
     *
     * @return void
     */
    public function setService(\XLite\Module\XC\CanadaPost\Model\DeliveryService $service = null)
    {
        $this->service = $service;
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
     * @return Option
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
     * Set name
     *
     * @param string $name
     * @return Option
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
     * Set mandatory
     *
     * @param boolean $mandatory
     * @return Option
     */
    public function setMandatory($mandatory)
    {
        $this->mandatory = $mandatory;
        return $this;
    }

    /**
     * Get mandatory
     *
     * @return boolean 
     */
    public function getMandatory()
    {
        return $this->mandatory;
    }

    /**
     * Set qualifierRequired
     *
     * @param boolean $qualifierRequired
     * @return Option
     */
    public function setQualifierRequired($qualifierRequired)
    {
        $this->qualifierRequired = $qualifierRequired;
        return $this;
    }

    /**
     * Get qualifierRequired
     *
     * @return boolean 
     */
    public function getQualifierRequired()
    {
        return $this->qualifierRequired;
    }

    /**
     * Set qualifierMax
     *
     * @param decimal $qualifierMax
     * @return Option
     */
    public function setQualifierMax($qualifierMax)
    {
        $this->qualifierMax = $qualifierMax;
        return $this;
    }

    /**
     * Get qualifierMax
     *
     * @return decimal 
     */
    public function getQualifierMax()
    {
        return $this->qualifierMax;
    }

    /**
     * Get service
     *
     * @return \XLite\Module\XC\CanadaPost\Model\DeliveryService 
     */
    public function getService()
    {
        return $this->service;
    }
}
