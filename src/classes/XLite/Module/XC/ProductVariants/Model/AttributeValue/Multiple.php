<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductVariants\Model\AttributeValue;

/**
 * Abstract multiple attribute value
 *
 */
abstract class Multiple extends \XLite\Model\AttributeValue\Multiple implements \XLite\Base\IDecorator
{
    /**
     * Check is apply or nor
     *
     * @return boolean
     */
    protected function isApply()
    {
        $result = parent::isApply();

        if ($result && $this->getProduct()->mustHaveVariants()) {
            foreach ($this->getProduct()->getVariantsAttributes() as $attr) {
                if ($attr->getId() == $this->getAttribute()->getId()) {
                    // Current attribute is used in variants, return false
                    $result = false;
                    break;
                }
            }
        }

        return $result;
    }

    /**
     * Get current product variant
     *
     * @return \XLite\Module\XC\ProductVariant\Model\ProductVariant
     */
    protected function getVariant()
    {
        $variant = null;

        if ($this->getProduct()->mustHaveVariants()) {
            $variant = $this->getProduct()->getVariant($this->getProduct()->getAttrValues() ?: null);
        }

        return $variant;
    }

    /**
     * Get price modifier base value
     *
     * @return float
     */
    protected function getModifierBasePrice()
    {
        return $this->getVariant() ? $this->getVariant()->getClearPrice() : parent::getModifierBasePrice();
    }

    /**
     * Get weight modifier base value
     *
     * @return float
     */
    protected function getModifierBaseWeight()
    {
        return $this->getVariant() ? $this->getVariant()->getClearWeight() : parent::getModifierBaseWeight();
    }

    /**
     * Set priceModifier
     *
     * @param decimal $priceModifier
     * @return Multiple
     */
    public function setPriceModifier($priceModifier)
    {
        $this->priceModifier = $priceModifier;
        return $this;
    }

    /**
     * Get priceModifier
     *
     * @return decimal 
     */
    public function getPriceModifier()
    {
        return $this->priceModifier;
    }

    /**
     * Set priceModifierType
     *
     * @param string $priceModifierType
     * @return Multiple
     */
    public function setPriceModifierType($priceModifierType)
    {
        $this->priceModifierType = $priceModifierType;
        return $this;
    }

    /**
     * Get priceModifierType
     *
     * @return string 
     */
    public function getPriceModifierType()
    {
        return $this->priceModifierType;
    }

    /**
     * Set weightModifier
     *
     * @param decimal $weightModifier
     * @return Multiple
     */
    public function setWeightModifier($weightModifier)
    {
        $this->weightModifier = $weightModifier;
        return $this;
    }

    /**
     * Get weightModifier
     *
     * @return decimal 
     */
    public function getWeightModifier()
    {
        return $this->weightModifier;
    }

    /**
     * Set weightModifierType
     *
     * @param string $weightModifierType
     * @return Multiple
     */
    public function setWeightModifierType($weightModifierType)
    {
        $this->weightModifierType = $weightModifierType;
        return $this;
    }

    /**
     * Get weightModifierType
     *
     * @return string 
     */
    public function getWeightModifierType()
    {
        return $this->weightModifierType;
    }

    /**
     * Set defaultValue
     *
     * @param boolean $defaultValue
     * @return Multiple
     */
    public function setDefaultValue($defaultValue)
    {
        $this->defaultValue = $defaultValue;
        return $this;
    }

    /**
     * Get defaultValue
     *
     * @return boolean 
     */
    public function getDefaultValue()
    {
        return $this->defaultValue;
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
     * Set product
     *
     * @param \XLite\Model\Product $product
     * @return Multiple
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
     * Set attribute
     *
     * @param \XLite\Model\Attribute $attribute
     * @return Multiple
     */
    public function setAttribute(\XLite\Model\Attribute $attribute = null)
    {
        $this->attribute = $attribute;
        return $this;
    }

    /**
     * Get attribute
     *
     * @return \XLite\Model\Attribute 
     */
    public function getAttribute()
    {
        return $this->attribute;
    }
}
