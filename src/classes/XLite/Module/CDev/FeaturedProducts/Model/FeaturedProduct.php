<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\FeaturedProducts\Model;

/**
 * Featured Product
 *
 * @Entity
 * @Table (name="featured_products",
 *      uniqueConstraints={
 *             @UniqueConstraint (name="pair", columns={"category_id","product_id"})
 *      }
 * )
 */

class FeaturedProduct extends \XLite\Model\AEntity
{
    /**
     * Session cell name
     */
    const SESSION_CELL_NAME = 'featuredProductsSearch';

    /**
     * Product + category link unique id
     *
     * @var integer
     *
     * @Id
     * @GeneratedValue (strategy="AUTO")
     * @Column         (type="integer")
     */
    protected $id;

    /**
     * Sort position
     *
     * @var integer
     *
     * @Column (type="integer")
     */
    protected $orderBy = 0;

    /**
     * Product (relation)
     *
     * @var \XLite\Model\Product
     *
     * @ManyToOne  (targetEntity="XLite\Model\Product", inversedBy="featuredProducts")
     * @JoinColumn (name="product_id", referencedColumnName="product_id", onDelete="CASCADE")
     */
    protected $product;

    /**
     * Category (relation)
     *
     * @var \XLite\Model\Category
     *
     * @ManyToOne  (targetEntity="XLite\Model\Category", inversedBy="featuredProducts")
     * @JoinColumn (name="category_id", referencedColumnName="category_id", onDelete="CASCADE")
     */
    protected $category;


    /**
     * SKU getter
     *
     * @return string
     */
    public function getSku()
    {
        return $this->getProduct()->getSku();
    }

    /**
     * Price getter
     *
     * @return double
     */
    public function getPrice()
    {
        return $this->getProduct()->getPrice();
    }

    /**
     * Amount getter
     *
     * @return integer
     */
    public function getAmount()
    {
        return $this->getProduct()->getPublicAmount();
    }

    /**
     * Get position
     *
     * @return integer
     */
    public function getPosition()
    {
        return $this->getOrderBy();
    }

    /**
     * Set position
     *
     * @param integer $position Category position
     *
     * @return void
     */
    public function setPosition($position)
    {
        return $this->setOrderBy($position);
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
     * Set orderBy
     *
     * @param integer $orderBy
     * @return FeaturedProduct
     */
    public function setOrderBy($orderBy)
    {
        $this->orderBy = $orderBy;
        return $this;
    }

    /**
     * Get orderBy
     *
     * @return integer 
     */
    public function getOrderBy()
    {
        return $this->orderBy;
    }

    /**
     * Set product
     *
     * @param \XLite\Model\Product $product
     * @return FeaturedProduct
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
     * Set category
     *
     * @param \XLite\Model\Category $category
     * @return FeaturedProduct
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
