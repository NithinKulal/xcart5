<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Wholesale\Model;

/**
 * Wholesale price model (product)
 *
 * @Entity
 * @Table  (name="wholesale_prices",
 *      indexes={
 *          @Index (name="range", columns={"product_id", "membership_id", "quantityRangeBegin", "quantityRangeEnd"})
 *      }
 * )
 */
class WholesalePrice extends \XLite\Module\CDev\Wholesale\Model\Base\AWholesalePrice
{
    /**
     * Relation to a product entity
     *
     * @var \XLite\Model\Product
     *
     * @ManyToOne  (targetEntity="XLite\Model\Product")
     * @JoinColumn (name="product_id", referencedColumnName="product_id", onDelete="CASCADE")
     */
    protected $product;

    /**
     * Return owner
     *
     * @return \XLite\Model\Product
     */
    public function getOwner()
    {
        return $this->getProduct();
    }

    /**
     * Set owner
     *
     * @param \XLite\Model\Product $owner Owner
     *
     * @return void
     */
    public function setOwner($owner)
    {
        return $this->setProduct($owner);
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
     * Set price
     *
     * @param float $price
     * @return WholesalePrice
     */
    public function setPrice($price)
    {
        $this->price = $price;
        return $this;
    }

    /**
     * Get price
     *
     * @return float 
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set quantityRangeBegin
     *
     * @param integer $quantityRangeBegin
     * @return WholesalePrice
     */
    public function setQuantityRangeBegin($quantityRangeBegin)
    {
        $this->quantityRangeBegin = $quantityRangeBegin;
        return $this;
    }

    /**
     * Get quantityRangeBegin
     *
     * @return integer 
     */
    public function getQuantityRangeBegin()
    {
        return $this->quantityRangeBegin;
    }

    /**
     * Set quantityRangeEnd
     *
     * @param integer $quantityRangeEnd
     * @return WholesalePrice
     */
    public function setQuantityRangeEnd($quantityRangeEnd)
    {
        $this->quantityRangeEnd = $quantityRangeEnd;
        return $this;
    }

    /**
     * Get quantityRangeEnd
     *
     * @return integer 
     */
    public function getQuantityRangeEnd()
    {
        return $this->quantityRangeEnd;
    }

    /**
     * Set product
     *
     * @param \XLite\Model\Product $product
     * @return WholesalePrice
     */
    public function setProduct(\XLite\Model\Product $product = null)
    {
        $this->product = $product;
        return $this;
    }

    /**
     * Get product
     *
     * @return \XLite\Model\Product 
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * Set membership
     *
     * @param \XLite\Model\Membership $membership
     * @return WholesalePrice
     */
    public function setMembership(\XLite\Model\Membership $membership = null)
    {
        $this->membership = $membership;
        return $this;
    }

    /**
     * Get membership
     *
     * @return \XLite\Model\Membership 
     */
    public function getMembership()
    {
        return $this->membership;
    }
}
