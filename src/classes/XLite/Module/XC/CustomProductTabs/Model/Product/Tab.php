<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

// vim: set ts=4 sw=4 sts=4 et:

namespace XLite\Module\XC\CustomProductTabs\Model\Product;

/**
 * The "tab" model class
 *
 * @Entity
 * @Table  (name="product_tabs")
 */
class Tab extends \XLite\Model\Base\I18n
{
    /**
     * Tab unique ID
     *
     * @var integer
     *
     * @Id
     * @GeneratedValue (strategy="AUTO")
     * @Column         (type="integer", options={ "unsigned": true })
     */
    protected $id;

    /**
     * Tab position
     *
     * @var integer
     *
     * @Column (type="integer")
     */
    protected $position = 0;

    /**
     * Is tab available or not
     *
     * @var boolean
     *
     * @Column (type="boolean")
     */
    protected $enabled = true;

    /**
     * Tab product
     *
     * @var \XLite\Model\Product
     * @ManyToOne  (targetEntity="XLite\Model\Product", inversedBy="tabs")
     * @JoinColumn (name="product_id", referencedColumnName="product_id", onDelete="CASCADE")
     */
    protected $product;

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
     * @return Tab
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
     * Set enabled
     *
     * @param boolean $enabled
     * @return Tab
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
        return $this;
    }

    /**
     * Get enabled
     *
     * @return boolean 
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Set product
     *
     * @param \XLite\Model\Product $product
     * @return Tab
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
