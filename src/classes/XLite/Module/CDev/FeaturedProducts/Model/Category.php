<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\FeaturedProducts\Model;

/**
 * Category model
 */
class Category extends \XLite\Model\Category implements \XLite\Base\IDecorator
{
    /**
     * Featured products (relation)
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @OneToMany (targetEntity="XLite\Module\CDev\FeaturedProducts\Model\FeaturedProduct", mappedBy="category", cascade={"all"})
     */
    protected $featuredProducts;

    /**
     * Constructor
     *
     * @param array $data Entity properties OPTIONAL
     *
     * @return void
     */
    public function __construct(array $data = array())
    {
        $this->featuredProducts = new \Doctrine\Common\Collections\ArrayCollection();

        parent::__construct($data);
    }

    public function getFeaturedProductsCount()
    {
        return $this->getFeaturedProducts()->count() ?: 0;
    }

    /**
     * Add featuredProducts
     *
     * @param \XLite\Module\CDev\FeaturedProducts\Model\FeaturedProduct $featuredProducts
     * @return Category
     */
    public function addFeaturedProducts(\XLite\Module\CDev\FeaturedProducts\Model\FeaturedProduct $featuredProducts)
    {
        $this->featuredProducts[] = $featuredProducts;
        return $this;
    }

    /**
     * Get featuredProducts
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getFeaturedProducts()
    {
        return $this->featuredProducts;
    }
}
