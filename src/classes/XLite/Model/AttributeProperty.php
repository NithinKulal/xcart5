<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model;

/**
 * Attribute option
 *
 * @Entity
 * @Table  (name="attribute_properties")
 */
class AttributeProperty extends \XLite\Model\AEntity
{
    /**
     * ID
     *
     * @var integer
     *
     * @Id
     * @GeneratedValue (strategy="AUTO")
     * @Column         (type="integer", options={ "unsigned": true })
     */
    protected $id;

    /**
     * Product
     *
     * @var \XLite\Model\Product
     *
     * @ManyToOne  (targetEntity="XLite\Model\Product")
     * @JoinColumn (name="product_id", referencedColumnName="product_id", onDelete="CASCADE")
     */
    protected $product;

    /**
     * Attribute
     *
     * @var \XLite\Model\Attribute
     *
     * @ManyToOne  (targetEntity="XLite\Model\Attribute", inversedBy="attribute_properties")
     * @JoinColumn (name="attribute_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $attribute;

    /**
     * Position
     *
     * @var integer
     *
     * @Column (type="integer")
     */
    protected $position = 0;

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
     * Set position
     *
     * @param integer $position
     * @return AttributeProperty
     */
    public function setPosition($position)
    {
        $this->position = $position;
        return $this;
    }

    /**
     * Get position
     *
     * @return integer 
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set product
     *
     * @param \XLite\Model\Product $product
     * @return AttributeProperty
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
     * @return AttributeProperty
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
