<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\VolumeDiscounts\Model;

/**
 * Volume discount model
 *
 * @Entity
 * @Table  (name="volume_discounts",
 *      indexes={
 *          @Index (name="range", columns={"subtotalRangeBegin", "subtotalRangeEnd"})
 *      }
 * )
 */
class VolumeDiscount extends \XLite\Model\AEntity
{
    const TYPE_PERCENT  = '%';
    const TYPE_ABSOLUTE = '$';


    /**
     * Discount unique ID
     *
     * @var   integer
     *
     * @Id
     * @GeneratedValue (strategy="AUTO")
     * @Column         (type="integer", options={ "unsigned": true })
     */
    protected $id;

    /**
     * Value
     *
     * @var   float
     *
     * @Column (type="decimal", precision=14, scale=4)
     */
    protected $value = 0.0000;

    /**
     * Type
     *
     * @var   string
     *
     * @Column (type="string", options={ "fixed": true }, length=1)
     */
    protected $type = self::TYPE_PERCENT;

    /**
     * Subtotal range (begin)
     *
     * @var   float
     *
     * @Column (type="decimal", precision=14, scale=4)
     */
    protected $subtotalRangeBegin = 0;

    /**
     * Subtotal range (end)
     *
     * @var   float
     *
     * @Column (type="decimal", precision=14, scale=4)
     */
    protected $subtotalRangeEnd = 0;

    /**
     * Membership
     *
     * @var   \XLite\Model\Membership
     *
     * @ManyToOne (targetEntity="XLite\Model\Membership")
     * @JoinColumn (name="membership_id", referencedColumnName="membership_id", onDelete="CASCADE")
     */
    protected $membership;


    /**
     * Check - discount is absolute or not
     *
     * @return boolean
     */
    public function isAbsolute()
    {
        return static::TYPE_ABSOLUTE == $this->getType();
    }

    /**
     * Get discount amount
     *
     * @param \XLite\Model\Order $order Order
     *
     * @return float
     */
    public function getAmount(\XLite\Model\Order $order)
    {
        $discount = $this->isAbsolute()
            ? $this->getValue()
            : ($order->getSubtotal() * $this->getValue() / 100);

        return min($discount, $order->getSubtotal());
    }

    /**
     * Get fingerprint 
     * 
     * @return string
     */
    public function getFingerprint()
    {
        return $this->getSubtotalRangeBegin() . ':'
            . ($this->getMembership() ? $this->getMembership()->getMembershipId() : 0);
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
     * Set value
     *
     * @param decimal $value
     * @return VolumeDiscount
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * Get value
     *
     * @return decimal 
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return VolumeDiscount
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set subtotalRangeBegin
     *
     * @param decimal $subtotalRangeBegin
     * @return VolumeDiscount
     */
    public function setSubtotalRangeBegin($subtotalRangeBegin)
    {
        $this->subtotalRangeBegin = $subtotalRangeBegin;
        return $this;
    }

    /**
     * Get subtotalRangeBegin
     *
     * @return decimal 
     */
    public function getSubtotalRangeBegin()
    {
        return $this->subtotalRangeBegin;
    }

    /**
     * Set subtotalRangeEnd
     *
     * @param decimal $subtotalRangeEnd
     * @return VolumeDiscount
     */
    public function setSubtotalRangeEnd($subtotalRangeEnd)
    {
        $this->subtotalRangeEnd = $subtotalRangeEnd;
        return $this;
    }

    /**
     * Get subtotalRangeEnd
     *
     * @return decimal 
     */
    public function getSubtotalRangeEnd()
    {
        return $this->subtotalRangeEnd;
    }

    /**
     * Set membership
     *
     * @param \XLite\Model\Membership $membership
     * @return VolumeDiscount
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
}
