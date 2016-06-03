<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Wholesale\Model;

/**
 * Minimum purchase quantity
 *
 * @Entity
 * @Table (name="product_min_quantities",
 *      uniqueConstraints={
 *          @UniqueConstraint (name="pair", columns={"product_id","membership_id"})
 *      }
 * )
 */
class MinQuantity extends \XLite\Model\AEntity
{
    /**
     * Primary key
     *
     * @var   integer
     *
     * @Id
     * @GeneratedValue (strategy="AUTO")
     * @Column         (type="integer", options={ "unsigned": true })
     */
    protected $id;

    /**
     * Minimum product quantity
     *
     * @var   integer
     *
     * @Column (type="integer", options={ "unsigned": true })
     */
    protected $quantity = 1;

    /**
     * Relation to a membership entity
     *
     * @var   \XLite\Model\Membership
     *
     * @ManyToOne  (targetEntity="XLite\Model\Membership", inversedBy="minQuantities")
     * @JoinColumn (name="membership_id", referencedColumnName="membership_id", onDelete="CASCADE")
     */
    protected $membership;

    /**
     * Relation to a product entity
     *
     * @var   \XLite\Model\Product
     *
     * @ManyToOne  (targetEntity="XLite\Model\Product", inversedBy="categoryProducts")
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
     * Set quantity
     *
     * @param integer $quantity
     * @return MinQuantity
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
        return $this;
    }

    /**
     * Get quantity
     *
     * @return integer 
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Set membership
     *
     * @param \XLite\Model\Membership $membership
     * @return MinQuantity
     */
    public function setMembership(\XLite\Model\Membership $membership = null)
    {
        $this->membership = $membership;
        return $this;
    }

    /**
     * Get membership
     *
     * @return \XLite\Model\Membership 
     */
    public function getMembership()
    {
        return $this->membership;
    }

    /**
     * Set product
     *
     * @param \XLite\Model\Product $product
     * @return MinQuantity
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
