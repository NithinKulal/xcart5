<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductVariants\Model\AttributeValue;

/**
 * Attribute value (select)
 *
 */
class AttributeValueSelect extends \XLite\Model\AttributeValue\AttributeValueSelect implements \XLite\Base\IDecorator
{
    /**
     * Variants
     *
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ManyToMany (targetEntity="XLite\Module\XC\ProductVariants\Model\ProductVariant", mappedBy="attributeValueS", cascade={"all"})
     */
    protected $variants;

    /**
     * @var boolean
     */
    protected $variantAvailable = true;

    /**
     * Constructor
     *
     * @param array $data Entity properties OPTIONAL
     */
    public function __construct(array $data = array())
    {
        $this->variants = new \Doctrine\Common\Collections\ArrayCollection();

        parent::__construct($data);
    }

    /**
     * Add variants
     *
     * @param \XLite\Module\XC\ProductVariants\Model\ProductVariant $variants
     */
    public function addVariants(\XLite\Module\XC\ProductVariants\Model\ProductVariant $variants)
    {
        $this->variants[] = $variants;
    }

    /**
     * Get variants
     *
     * @return \Doctrine\Common\Collections\Collection|\XLite\Module\XC\ProductVariants\Model\ProductVariant[]
     */
    public function getVariants()
    {
        return $this->variants;
    }

    /**
     * @return boolean
     */
    public function isVariantAvailable()
    {
        return $this->variantAvailable;
    }

    /**
     * @param boolean $variantAvailable
     */
    public function setVariantAvailable($variantAvailable)
    {
        $this->variantAvailable = $variantAvailable;
    }
}
