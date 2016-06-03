<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductVariants\Model\Image\ProductVariant;

/**
 * Product variant image
 *
 * @Entity
 * @Table  (name="product_variant_images")
 */
class Image extends \XLite\Model\Base\Image
{
    /**
     * Product variant
     *
     * @var \XLite\Module\XC\ProductVariants\Model\ProductVariant
     *
     * @OneToOne   (targetEntity="XLite\Module\XC\ProductVariants\Model\ProductVariant", inversedBy="image")
     * @JoinColumn (name="product_variant_id", referencedColumnName="id")
     */
    protected $product_variant;

    /**
     * Alternative image text
     *
     * @var string
     *
     * @Column (type="string", length=255)
     */
    protected $alt = '';


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
     * Set product_variant
     *
     * @param \XLite\Module\XC\ProductVariants\Model\ProductVariant $productVariant
     * @return Image
     */
    public function setProductVariant(\XLite\Module\XC\ProductVariants\Model\ProductVariant $productVariant = null)
    {
        $this->product_variant = $productVariant;
        return $this;
    }

    /**
     * Get product_variant
     *
     * @return \XLite\Module\XC\ProductVariants\Model\ProductVariant 
     */
    public function getProductVariant()
    {
        return $this->product_variant;
    }
}
