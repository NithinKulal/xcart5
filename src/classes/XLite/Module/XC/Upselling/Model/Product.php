<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Upselling\Model;

/**
 * Product
 */
class Product extends \XLite\Model\Product implements \XLite\Base\IDecorator
{
    /**
     * Links to related products (relation [this product] -> [related product])
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @OneToMany (targetEntity="XLite\Module\XC\Upselling\Model\UpsellingProduct", mappedBy="parentProduct", cascade={"all"})
     */
    protected $upsellingProducts;

    /**
     * Back links from related products (back relation [related product] -> [this product])
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @OneToMany (targetEntity="XLite\Module\XC\Upselling\Model\UpsellingProduct", mappedBy="product", cascade={"all"})
     */
    protected $upsellingParentProducts;

    /**
     * Constructor
     *
     * @param array $data Entity properties OPTIONAL
     *
     * @return void
     */
    public function __construct(array $data = array())
    {
        $this->upsellingProducts = new \Doctrine\Common\Collections\ArrayCollection();
        $this->upsellingParentProducts = new \Doctrine\Common\Collections\ArrayCollection();

        parent::__construct($data);
    }

    /**
     * Clone
     *
     * @return \XLite\Model\AEntity
     */
    public function cloneEntity()
    {
        $newProduct = parent::cloneEntity();

        if ($this->getUpsellingProducts()) {
            $this->cloneUpsellingLinks($newProduct, false);
        }

        if ($this->getUpsellingParentProducts()) {
            $this->cloneUpsellingLinks($newProduct, true);
        }

        return $newProduct;
    }

    /**
     * Clone upselling links
     *
     * @param \XLite\Model\Product $product   Cloned product object
     * @param boolean              $backLinks Flag: true - create back links, false - direct links
     *
     * @return void
     */
    protected function cloneUpsellingLinks($product, $backLinks = false)
    {
        $upsellingLinks = $backLinks
            ? $this->getUpsellingParentProducts()
            : $this->getUpsellingProducts();

        foreach ($upsellingLinks as $up) {

            $newUp = new \XLite\Module\XC\Upselling\Model\UpsellingProduct();

            if ($backLinks) {
                $newUp->setProduct($product);
                $newUp->setParentProduct($up->getParentProduct());

            } else {
                $newUp->setProduct($up->getProduct());
                $newUp->setParentProduct($product);
            }

            $newUp->setOrderBy($up->getOrderBy());

            \XLite\Core\Database::getEM()->persist($newUp);
        }
    }

    /**
     * Add upsellingProducts
     *
     * @param \XLite\Module\XC\Upselling\Model\UpsellingProduct $upsellingProducts
     * @return Product
     */
    public function addUpsellingProducts(\XLite\Module\XC\Upselling\Model\UpsellingProduct $upsellingProducts)
    {
        $this->upsellingProducts[] = $upsellingProducts;
        return $this;
    }

    /**
     * Get upsellingProducts
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUpsellingProducts()
    {
        return $this->upsellingProducts;
    }

    /**
     * Add upsellingParentProducts
     *
     * @param \XLite\Module\XC\Upselling\Model\UpsellingProduct $upsellingParentProducts
     * @return Product
     */
    public function addUpsellingParentProducts(\XLite\Module\XC\Upselling\Model\UpsellingProduct $upsellingParentProducts)
    {
        $this->upsellingParentProducts[] = $upsellingParentProducts;
        return $this;
    }

    /**
     * Get upsellingParentProducts
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUpsellingParentProducts()
    {
        return $this->upsellingParentProducts;
    }
}
