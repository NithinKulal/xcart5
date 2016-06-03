<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\AttributeValue;

/**
 * Attribute value (text)
 *
 * @Entity
 * @Table  (name="attribute_values_text",
 *      indexes={
 *          @Index (name="product_id", columns={"product_id"}),
 *          @Index (name="attribute_id", columns={"attribute_id"})
 *      }
 * )
 */
class AttributeValueText extends \XLite\Model\AttributeValue\AAttributeValue
{
    /**
     * Editable flag
     *
     * @var boolean
     *
     * @Column (type="boolean")
     */
    protected $editable = false;

    /**
     * Return diff
     *
     * @param array oldValues Old values
     * @param array newValues New values
     *
     * @return array
     */
    static public function getDiff(array $oldValues, array $newValues)
    {
        $diff = array();
        if ($newValues) {
            foreach ($newValues as $attributeId => $value) {
                if (
                    !isset($oldValues[$attributeId])
                    || $value != $oldValues[$attributeId]
                ) {
                    $diff[$attributeId] = $value;
                }
            }
        }

        return $diff;
    }

    /**
     * Return attribute value as string
     *
     * @return string
     */
    public function asString()
    {
        return $this->getValue();
    }

    /**
     * Set editable
     *
     * @param boolean $editable
     * @return AttributeValueText
     */
    public function setEditable($editable)
    {
        $this->editable = $editable;
        return $this;
    }

    /**
     * Get editable
     *
     * @return boolean 
     */
    public function getEditable()
    {
        return $this->editable;
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
     * @return AttributeValueText
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
     * @return AttributeValueText
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
