<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */


namespace XLite\Module\XC\FreeShipping\Model;

/**
 * Decorate product model
 */
class Product extends \XLite\Model\Product implements \XLite\Base\IDecorator
{
    /**
     * Is free shipping available for the product
     *
     * @var boolean
     *
     * @Column (type="boolean")
     */
    protected $freeShip = false;

    /**
     * Shipping freight fixed fee
     *
     * @var float
     *
     * @Column (type="decimal", precision=14, scale=4)
     */
    protected $freightFixedFee = 0;

    /**
     * Set freeShip
     *
     * @param boolean $freeShip
     * @return Product
     */
    public function setFreeShip($freeShip)
    {
        $this->freeShip = $freeShip;
        return $this;
    }

    /**
     * Get freeShip
     *
     * @return boolean 
     */
    public function getFreeShip()
    {
        return $this->freeShip;
    }

    /**
     * Set freightFixedFee
     *
     * @param decimal $freightFixedFee
     * @return Product
     */
    public function setFreightFixedFee($freightFixedFee)
    {
        $this->freightFixedFee = $freightFixedFee;
        return $this;
    }

    /**
     * Get freightFixedFee
     *
     * @return decimal 
     */
    public function getFreightFixedFee()
    {
        return $this->freightFixedFee;
    }
}
