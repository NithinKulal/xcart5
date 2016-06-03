<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CustomProductTabs\Model;

/**
 * The "Product" decoration model class
 */
abstract class Product extends \XLite\Model\Product implements \XLite\Base\IDecorator
{
    /**
     * Order tabs
     *
     * @var \Doctrine\Common\Collections\Collection
     *
     * @OrderBy   ({"position" = "ASC"})
     * @OneToMany (targetEntity="XLite\Module\XC\CustomProductTabs\Model\Product\Tab", mappedBy="product", cascade={"all"})
     */
    protected $tabs;

    /**
     * Constructor
     *
     * @param array $data Entity properties OPTIONAL
     *
     * @return void
     */
    public function __construct(array $data = array())
    {
        $this->tabs = new \Doctrine\Common\Collections\ArrayCollection();

        parent::__construct($data);
    }

    /**
     * Clone product
     *
     * @return \XLite\Model\AEntity
     */
    public function cloneEntity()
    {
        $newProduct = parent::cloneEntity();

        if ($this->getTabs()) {
            foreach ($this->getTabs() as $tab) {
                $newTab = $tab->cloneEntity();
                $newTab->setProduct($newProduct);
                $newProduct->addTabs($newTab);

                \XLite\Core\Database::getEM()->persist($newTab);
            }
        }

        return $newProduct;
    }

    /**
     * Add tabs
     *
     * @param \XLite\Module\XC\CustomProductTabs\Model\Product\Tab $tabs
     * @return Product
     */
    public function addTabs(\XLite\Module\XC\CustomProductTabs\Model\Product\Tab $tabs)
    {
        $this->tabs[] = $tabs;
        return $this;
    }

    /**
     * Get tabs
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTabs()
    {
        return $this->tabs;
    }
}
