<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Image\Category;

/**
 * Category
 *
 * @Entity
 * @Table  (name="category_images")
 */
class Image extends \XLite\Model\Base\Image
{
    /**
     * Relation to a category entity
     *
     * @var \XLite\Model\Category
     *
     * @OneToOne   (targetEntity="XLite\Model\Category", inversedBy="image")
     * @JoinColumn (name="category_id", referencedColumnName="category_id", onDelete="CASCADE")
     */
    protected $category;

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
     * Set category
     *
     * @param \XLite\Model\Category $category
     * @return Image
     */
    public function setCategory(\XLite\Model\Category $category = null)
    {
        $this->category = $category;
        return $this;
    }

    /**
     * Get category
     *
     * @return \XLite\Model\Category 
     */
    public function getCategory()
    {
        return $this->category;
    }
}
