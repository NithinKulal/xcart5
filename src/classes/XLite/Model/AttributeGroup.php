<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model;

/**
 * Attribute group
 *
 * @Entity
 * @Table  (name="attribute_groups")
 */
class AttributeGroup extends \XLite\Model\Base\I18n
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
     * Position
     *
     * @var   integer
     * @see   ____var_see____
     * @since 1.0.0
     *
     * @Column (type="integer")
     */
    protected $position = 0;

    /**
     * Product class 
     *
     * @var \XLite\Model\ProductClass
     *
     * @ManyToOne  (targetEntity="XLite\Model\ProductClass", inversedBy="attribute_groups")
     * @JoinColumn (name="product_class_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $productClass;

    /**
     * Attributes
     *
     * @var \Doctrine\Common\Collections\Collection
     *
     * @OneToMany (targetEntity="XLite\Model\Attribute", mappedBy="attributeGroup")
     */
    protected $attributes;

    /**
     * Constructor
     *
     * @param array $data Entity properties OPTIONAL
     *
     * @return void
     */
    public function __construct(array $data = array())
    {
        $this->attributes = new \Doctrine\Common\Collections\ArrayCollection();

        parent::__construct($data);
    }

    /**
     * Return number of attributes associated with this class
     *
     * @return integer
     */
    public function getAttributesCount()
    {
        return count($this->getAttributes());
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
     * Set position
     *
     * @param integer $position
     * @return AttributeGroup
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
     * Set productClass
     *
     * @param \XLite\Model\ProductClass $productClass
     * @return AttributeGroup
     */
    public function setProductClass(\XLite\Model\ProductClass $productClass = null)
    {
        $this->productClass = $productClass;
        return $this;
    }

    /**
     * Get productClass
     *
     * @return \XLite\Model\ProductClass 
     */
    public function getProductClass()
    {
        return $this->productClass;
    }

    /**
     * Add attributes
     *
     * @param \XLite\Model\Attribute $attributes
     * @return AttributeGroup
     */
    public function addAttributes(\XLite\Model\Attribute $attributes)
    {
        $this->attributes[] = $attributes;
        return $this;
    }

    /**
     * Get attributes
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAttributes()
    {
        return $this->attributes;
    }
}
