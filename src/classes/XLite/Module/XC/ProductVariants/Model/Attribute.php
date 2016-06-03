<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductVariants\Model;

/**
 * Attribute
 *
 */
class Attribute extends \XLite\Model\Attribute implements \XLite\Base\IDecorator
{
    /**
     * Variants products
     *
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ManyToMany (targetEntity="XLite\Model\Product", mappedBy="variantsAttributes", cascade={"merge","detach"})
     */
    protected $variantsProducts;

    /**
     * Get attribute value
     *
     * @param \XLite\Model\Product $product Product
     *
     * @return mixed
     */
    public function getDefaultAttributeValue(\XLite\Model\Product $product)
    {
        $attributeValue = null;
        if ($product->mustHaveVariants()) {
            $variant = $product->getDefaultVariant();
            if ($variant) {
                foreach ($variant->getValues() as $av) {
                    if ($av->getAttribute()->getId() == $this->getId()) {
                        $attributeValue = $av;
                        break;
                    }
                }
            }
        }

        return $attributeValue ?: parent::getDefaultAttributeValue($product);
    }

    /**
     * Check attribute is variable or not
     *
     * @param \XLite\Model\Product $product Product
     *
     * @return boolean
     */
    public function isVariable(\XLite\Model\Product $product)
    {
        $result = false;

        foreach ($product->getVariantsAttributes() as $a) {
            if ($a->getId() == $this->getId()) {
                $result = true;
                break;
            }
        }

        return $result;
    }

    /**
     * Add variantsProducts
     *
     * @param \XLite\Model\Product $variantsProducts
     * @return Attribute
     */
    public function addVariantsProducts(\XLite\Model\Product $variantsProducts)
    {
        $this->variantsProducts[] = $variantsProducts;
        return $this;
    }

    /**
     * Get variantsProducts
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getVariantsProducts()
    {
        return $this->variantsProducts;
    }
}
