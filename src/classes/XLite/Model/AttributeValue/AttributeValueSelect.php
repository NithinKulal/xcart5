<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\AttributeValue;

/**
 * Attribute value (select)
 *
 * @Entity
 * @Table  (name="attribute_values_select",
 *      indexes={
 *          @Index (name="product_id", columns={"product_id"}),
 *          @Index (name="attribute_id", columns={"attribute_id"}),
 *          @Index (name="attribute_option_id", columns={"attribute_option_id"})
 *      }
 * )
 */
class AttributeValueSelect extends \XLite\Model\AttributeValue\Multiple
{
    /**
     * Attribute option
     *
     * @var \XLite\Model\AttributeOption
     *
     * @ManyToOne  (targetEntity="XLite\Model\AttributeOption")
     * @JoinColumn (name="attribute_option_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $attribute_option;

    /**
     * Return attribute value as string
     *
     * @return string
     */
    public function asString()
    {
        return $this->getAttributeOption()->getName();
    }

    /**
     * Clone
     *
     * @return \XLite\Model\AEntity
     */
    public function cloneEntity()
    {
        $newEntity = parent::cloneEntity();

        if ($this->getAttributeOption()) {
            if ($this->getAttribute()->getProduct()) {
                $attributeOption = $this->getAttributeOption()->cloneEntity();
                \XLite\Core\Database::getEM()->persist($attributeOption);

            } else {
                $attributeOption = $this->getAttributeOption();
            }
            $newEntity->setAttributeOption($attributeOption);
        }

        return $newEntity;
    }

    /**
     * Set priceModifier
     *
     * @param decimal $priceModifier
     * @return AttributeValueSelect
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
     * @return AttributeValueSelect
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
     * @return AttributeValueSelect
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
     * @return AttributeValueSelect
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
     * @return AttributeValueSelect
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
     * Set attribute_option
     *
     * @param \XLite\Model\AttributeOption $attributeOption
     * @return AttributeValueSelect
     */
    public function setAttributeOption(\XLite\Model\AttributeOption $attributeOption = null)
    {
        $this->attribute_option = $attributeOption;
        return $this;
    }

    /**
     * Get attribute_option
     *
     * @return \XLite\Model\AttributeOption 
     */
    public function getAttributeOption()
    {
        return $this->attribute_option;
    }

    /**
     * Set product
     *
     * @param \XLite\Model\Product $product
     * @return AttributeValueSelect
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
     * @return AttributeValueSelect
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
