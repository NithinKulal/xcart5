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
 * @Table  (name="attribute_options")
 */
class AttributeOption extends \XLite\Model\Base\I18n
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
     * Attribute
     *
     * @var \XLite\Model\Attribute
     *
     * @ManyToOne  (targetEntity="XLite\Model\Attribute", inversedBy="attribute_options")
     * @JoinColumn (name="attribute_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $attribute;

    /**
     * Add to new products or class’s assigns automatically
     *
     * @var boolean
     *
     * @Column (type="boolean")
     */
    protected $addToNew = false;

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
     * Set addToNew
     *
     * @param boolean $addToNew
     * @return AttributeOption
     */
    public function setAddToNew($addToNew)
    {
        $this->addToNew = $addToNew;
        return $this;
    }

    /**
     * Get addToNew
     *
     * @return boolean 
     */
    public function getAddToNew()
    {
        return $this->addToNew;
    }

    /**
     * Set attribute
     *
     * @param \XLite\Model\Attribute $attribute
     * @return AttributeOption
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
