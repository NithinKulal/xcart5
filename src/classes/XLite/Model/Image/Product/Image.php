<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Image\Product;

/**
 * Product image
 *
 * @Entity
 * @Table  (name="product_images")
 */
class Image extends \XLite\Model\Base\Image
{
    /**
     * Image position
     *
     * @var integer
     *
     * @Column (type="integer")
     */
    protected $orderby = 0;

    /**
     * Relation to a product entity
     *
     * @var \XLite\Model\Product
     *
     * @ManyToOne  (targetEntity="XLite\Model\Product", inversedBy="images")
     * @JoinColumn (name="product_id", referencedColumnName="product_id", onDelete="CASCADE")
     */
    protected $product;

    /**
     * Alternative image text
     *
     * @var string
     *
     * @Column (type="string", length=255)
     */
    protected $alt = '';

    /**
     * Set orderby
     *
     * @param integer $orderby
     * @return Image
     */
    public function setOrderby($orderby)
    {
        $this->orderby = $orderby;
        return $this;
    }

    /**
     * Get orderby
     *
     * @return integer 
     */
    public function getOrderby()
    {
        return $this->orderby;
    }

    /**
     * Set alt
     *
     * @param string $alt
     * @return Image
     */
    public function setAlt($alt)
    {
        $this->alt = $alt;
        return $this;
    }

    /**
     * Get alt
     *
     * @return string 
     */
    public function getAlt()
    {
        return $this->alt;
    }

    /**
     * Set product
     *
     * @param \XLite\Model\Product $product
     * @return Image
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
}
