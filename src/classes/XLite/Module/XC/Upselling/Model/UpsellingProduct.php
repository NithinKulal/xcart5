<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Upselling\Model;

/**
 * Upselling Product
 *
 * @Entity
 * @Table (name="upselling_products",
 *      indexes={
 *          @Index (name="parent_product_index", columns={"parent_product_id"}),
 *      }
 * )
 */
class UpsellingProduct extends \XLite\Model\AEntity
{
    /**
     * Session cell name
     */
    const SESSION_CELL_NAME = 'upsellingProductsSearch';

    /**
     * Unique id
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
     * @ManyToOne  (targetEntity="XLite\Model\Product", inversedBy="upsellingProducts")
     * @JoinColumn (name="product_id", referencedColumnName="product_id", onDelete="CASCADE")
     */
    protected $product;

    /**
     * Parent product (relation)
     *
     * @var \XLite\Model\Product
     *
     * @ManyToOne  (targetEntity="XLite\Model\Product", inversedBy="upsellingParentProducts")
     * @JoinColumn (name="parent_product_id", referencedColumnName="product_id", onDelete="CASCADE")
     */
    protected $parentProduct;


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
     * @return float
     */
    public function getPrice()
    {
        return $this->getProduct()->getPrice();
    }

    /**
     * Check if the bi-directional link is needed
     *
     * @return boolean
     */
    public function getBidirectional()
    {
        $linkData = array(
            'parentProduct' => $this->getProduct(),
            'product'       => $this->getParentProduct(),
        );

        return (bool)$this->getRepository()->findOneBy($linkData);
    }

    /**
     * Check if the bi-directional link is needed
     *
     * @return boolean
     */
    public function setBidirectional($newValue)
    {
        $newValue
            ? $this->getRepository()->addBidirectionalLink($this)
            : $this->getRepository()->deleteBidirectionalLink($this);
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
     * @param integer $position Upselling link position
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
     * @return UpsellingProduct
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
     * @return UpsellingProduct
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
     * Set parentProduct
     *
     * @param \XLite\Model\Product $parentProduct
     * @return UpsellingProduct
     */
    public function setParentProduct(\XLite\Model\Product $parentProduct = null)
    {
        $this->parentProduct = $parentProduct;
        return $this;
    }

    /**
     * Get parentProduct
     *
     * @return \XLite\Model\Product 
     */
    public function getParentProduct()
    {
        return $this->parentProduct;
    }
}
