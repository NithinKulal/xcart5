<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductFilter\Model;

/**
 * Product class 
 *
 */
class ProductClass extends \XLite\Model\ProductClass implements \XLite\Base\IDecorator
{
    /**
     * Categories
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ManyToMany (targetEntity="XLite\Model\Category", mappedBy="productClasses")
     */
    protected $categories;

    /**
     * Constructor
     *
     * @param array $data Entity properties OPTIONAL
     *
     * @return void
     */
    public function __construct(array $data = array())
    {
        $this->categories = new \Doctrine\Common\Collections\ArrayCollection();

        parent::__construct($data);
    }

    /**
     * Check if product class has non-empty atributes
     * 
     * @return boolean
     */
    public function hasNonEmptyAttributes()
    {
        $result = false;

        foreach ($this->getAttributes() as $attribute) {
            if (0 < count($attribute->getAttributeOptions())) {
                $result = true;
                break;
            }
        }

        return $result;
    }

    /**
     * ProductClass has at least one attributeGroup to show
     * 
     * @return boolean
     */
    public function hasNonEmptyGroups()
    {
        $result = false;

        foreach ($this->getAttributeGroups() as $group) {
            if ($group->hasNonEmptyAttributes()) {
                $result = true;
                break;
            }
        }

        return $result;
    }

    /**
     * Add categories
     *
     * @param \XLite\Model\Category $categories
     * @return ProductClass
     */
    public function addCategories(\XLite\Model\Category $categories)
    {
        $this->categories[] = $categories;
        return $this;
    }

    /**
     * Get categories
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCategories()
    {
        return $this->categories;
    }
}
