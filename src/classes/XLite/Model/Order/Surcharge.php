<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Order;

/**
 * Surcharge
 *
 * @Entity
 * @Table  (name="order_surcharges")
 */
class Surcharge extends \XLite\Model\Base\Surcharge
{
    /**
     * Surcharge owner (order)
     *
     * @var \XLite\Model\Order
     *
     * @ManyToOne  (targetEntity="XLite\Model\Order", inversedBy="surcharges")
     * @JoinColumn (name="order_id", referencedColumnName="order_id", onDelete="CASCADE")
     */
    protected $owner;

    /**
     * Get order
     *
     * @return void
     */
    public function getOrder()
    {
        return $this->getOwner();
    }

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
     * Set type
     *
     * @param string $type
     * @return Surcharge
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set code
     *
     * @param string $code
     * @return Surcharge
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
     * Set class
     *
     * @param string $class
     * @return Surcharge
     */
    public function setClass($class)
    {
        $this->class = $class;
        return $this;
    }

    /**
     * Get class
     *
     * @return string 
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * Set include
     *
     * @param boolean $include
     * @return Surcharge
     */
    public function setInclude($include)
    {
        $this->include = $include;
        return $this;
    }

    /**
     * Get include
     *
     * @return boolean 
     */
    public function getInclude()
    {
        return $this->include;
    }

    /**
     * Set available
     *
     * @param boolean $available
     * @return Surcharge
     */
    public function setAvailable($available)
    {
        $this->available = $available;
        return $this;
    }

    /**
     * Get available
     *
     * @return boolean 
     */
    public function getAvailable()
    {
        return $this->available;
    }

    /**
     * Get value
     *
     * @return float
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Surcharge
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Set weight
     *
     * @param integer $weight
     * @return Surcharge
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;
        return $this;
    }

    /**
     * Get weight
     *
     * @return integer 
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * Get owner
     *
     * @return \XLite\Model\Order 
     */
    public function getOwner()
    {
        return $this->owner;
    }
}
