<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\SalesTax\Model\Tax;

/**
 * Rate
 *
 * @Entity
 * @Table (name="sales_tax_rates")
 */
class Rate extends \XLite\Model\AEntity
{
    /**
     * Rate type codes
     */
    const TYPE_ABSOLUTE = 'a';
    const TYPE_PERCENT  = 'p';

    /**
     * Taxable bases
     */
    const TAXBASE_SUBTOTAL                     = 'ST';
    const TAXBASE_DISCOUNTED_SUBTOTAL          = 'DST';
    const TAXBASE_SUBTOTAL_SHIPPING            = 'ST+SH';
    const TAXBASE_DISCOUNTED_SUBTOTAL_SHIPPING = 'DST+SH';
    const TAXBASE_SHIPPING                     = 'SH';
    const TAXBASE_PERSONAL                     = 'P';

    /**
     * Rate unique ID
     *
     * @var integer
     *
     * @Id
     * @GeneratedValue (strategy="AUTO")
     * @Column         (type="integer", options={ "unsigned": true })
     */
    protected $id;

    /**
     * Value
     *
     * @var float
     *
     * @Column (type="decimal", precision=14, scale=4)
     */
    protected $value = 0.0000;

    /**
     * Type
     *
     * @var string
     *
     * @Column (type="string", options={ "fixed": true }, length=1)
     */
    protected $type = self::TYPE_PERCENT;

    /**
     * Position
     *
     * @var integer
     *
     * @Column (type="integer")
     */
    protected $position = 0;

    /**
     * Tax (relation)
     *
     * @var \XLite\Module\CDev\SalesTax\Model\Tax
     *
     * @ManyToOne  (targetEntity="XLite\Module\CDev\SalesTax\Model\Tax", inversedBy="rates")
     * @JoinColumn (name="tax_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $tax;

    /**
     * Zone (relation)
     *
     * @var \XLite\Model\Zone
     *
     * @ManyToOne  (targetEntity="XLite\Model\Zone")
     * @JoinColumn (name="zone_id", referencedColumnName="zone_id", onDelete="CASCADE")
     */
    protected $zone;

    /**
     * Tax class (relation)
     *
     * @var \XLite\Model\TaxClass
     *
     * @ManyToOne  (targetEntity="XLite\Model\TaxClass")
     * @JoinColumn (name="tax_class_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $taxClass;

    /**
     * Membership (relation)
     *
     * @var \XLite\Model\Membership
     *
     * @ManyToOne  (targetEntity="XLite\Model\Membership")
     * @JoinColumn (name="membership_id", referencedColumnName="membership_id", onDelete="CASCADE")
     */
    protected $membership;

    /**
     * For product without tax class
     *
     * @var boolean
     *
     * @Column (type="boolean")
     */
    protected $noTaxClass = false;

    /**
     * Taxable base
     *
     * @var string
     *
     * @Column (type="string", length=16)
     */
    protected $taxableBase = self::TAXBASE_SUBTOTAL;

    /**
     * Check if rate is applied by specified zones and membership
     *
     * @param array                   $zones      Zone id list
     * @param \XLite\Model\Membership $membership Membership OPTIONAL
     * @param \XLite\Model\TaxClass   $taxClass   Tax class OPTIONAL
     *
     * @return boolean
     */
    public function isApplied(
        array $zones,
        \XLite\Model\Membership $membership = null,
        \XLite\Model\TaxClass $taxClass = null
    ) {

        $result = $this->getZone() && in_array($this->getZone()->getZoneId(), $zones);

        if (
            $result
            && !\XLite\Core\Config::getInstance()->CDev->SalesTax->ignore_memberships
            && $this->getMembership()
        ) {
            $result = $membership && $this->getMembership()->getMembershipId() == $membership->getMembershipId();
        }

        return $result;
    }

    /**
     * Check if rate is applied to object
     *
     * @param mixed $object
     *
     * @return boolean
     */
    public function isAppliedToObject($object)
    {
        return (
            $this->getNoTaxClass()
            && !$object->getTaxClass()
        )
        || (
            !$this->getNoTaxClass()
            && (
                !$this->getTaxClass()
                || (
                    $object->getTaxClass()
                    && $object->getTaxClass()->getId() == $this->getTaxClass()->getId()
                )
            )
        );
    }

    // {{{ Calculation

    /**
     * Calculate
     *
     * @param array $items Items
     *
     * @return float
     */
    public function calculate(array $items)
    {
        $cost = 0;

        if ($this->getBasis($items) && $this->getQuantity($items)) {
            $cost = $this->getType() == static::TYPE_PERCENT
                ? $this->calculatePercent($items)
                : $this->calculateAbsolute($items);
        }

        return $cost;
    }

    /**
     * Calculate shipping tax cost
     *
     * @param float $shippingCost Shipping cost
     *
     * @return float
     */
    public function calculateShippingTax($shippingCost)
    {
        $cost = 0;

        if ($shippingCost) {
            $cost = $this->getType() == static::TYPE_PERCENT
                ? $shippingCost * $this->getValue() / 100
                : $this->getValue();
        }

        return $cost;
    }

    /**
     * Get list of allowed taxable base types
     *
     * @return array
     */
    protected static function getAllowedTaxableBaseTypes()
    {
        return array(
            static::TAXBASE_SUBTOTAL,
            static::TAXBASE_DISCOUNTED_SUBTOTAL,
            static::TAXBASE_SUBTOTAL_SHIPPING,
            static::TAXBASE_DISCOUNTED_SUBTOTAL_SHIPPING,
            static::TAXBASE_SHIPPING,
        );
    }

    /**
     * Get taxable base type
     *
     * @return string
     */
    public function getTaxableBaseType()
    {
        $result = \XLite\Core\Config::getInstance()->CDev->SalesTax->taxableBase;

        if (!in_array($result, static::getAllowedTaxableBaseTypes())) {
            $result = $this->getTaxableBase();
        }

        return $result;
    }

    /**
     * Get basis
     *
     * @param array $items Items
     *
     * @return float
     */
    protected function getBasis(array $items)
    {
        $basis = 0;

        foreach ($items as $item) {
            $basis += $this->getItemBasis($item);
        }

        return $basis;
    }

    /**
     * Get item basis
     *
     * @param \XLite\Model\OrderItem $item Order item
     *
     * @return float
     */
    protected function getItemBasis($item)
    {
        $basis = 0;

        $formulaParts = explode('+', $this->getTaxableBaseType());

        foreach ($formulaParts as $part) {
            switch ($part) {
                case 'ST':
                    $basis += $item->getSubtotal();
                    break;

                case 'DST':
                    $basis += $item->getDiscountedSubtotal();
                    break;

                case 'SH':
                    $basis += $item->getShippingCost();
                    break;
            }
        }

        return $basis;
    }

    /**
     * Get quantity
     *
     * @param array $items Items
     *
     * @return integer
     */
    protected function getQuantity(array $items)
    {
        $quantity = 0;

        foreach ($items as $item) {
            $quantity += $item->getAmount();
        }

        return $quantity;
    }

    /**
     * calculateExcludePercent
     *
     * @param array $items ____param_comment____
     *
     * @return array
     */
    protected function calculatePercent(array $items)
    {
        return $this->getBasis($items) * $this->getValue() / 100;
    }

    /**
     * Calculate tax as percent
     *
     * @param array $items Items
     *
     * @return array
     */
    protected function calculateAbsolute(array $items)
    {
        return $this->getValue() * $this->getQuantity($items);
    }

    // }}}

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
     * @return Rate
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
     * @return Rate
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
     * Set position
     *
     * @param integer $position
     * @return Rate
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
     * Set noTaxClass
     *
     * @param boolean $noTaxClass
     * @return Rate
     */
    public function setNoTaxClass($noTaxClass)
    {
        $this->noTaxClass = $noTaxClass;
        return $this;
    }

    /**
     * Get noTaxClass
     *
     * @return boolean 
     */
    public function getNoTaxClass()
    {
        return $this->noTaxClass;
    }

    /**
     * Set taxableBase
     *
     * @param string $taxableBase
     * @return Rate
     */
    public function setTaxableBase($taxableBase)
    {
        $this->taxableBase = $taxableBase;
        return $this;
    }

    /**
     * Get taxableBase
     *
     * @return string 
     */
    public function getTaxableBase()
    {
        return $this->taxableBase;
    }

    /**
     * Set tax
     *
     * @param \XLite\Module\CDev\SalesTax\Model\Tax $tax
     * @return Rate
     */
    public function setTax(\XLite\Module\CDev\SalesTax\Model\Tax $tax = null)
    {
        $this->tax = $tax;
        return $this;
    }

    /**
     * Get tax
     *
     * @return \XLite\Module\CDev\SalesTax\Model\Tax 
     */
    public function getTax()
    {
        return $this->tax;
    }

    /**
     * Set zone
     *
     * @param \XLite\Model\Zone $zone
     * @return Rate
     */
    public function setZone(\XLite\Model\Zone $zone = null)
    {
        $this->zone = $zone;
        return $this;
    }

    /**
     * Get zone
     *
     * @return \XLite\Model\Zone 
     */
    public function getZone()
    {
        return $this->zone;
    }

    /**
     * Set taxClass
     *
     * @param \XLite\Model\TaxClass $taxClass
     * @return Rate
     */
    public function setTaxClass(\XLite\Model\TaxClass $taxClass = null)
    {
        $this->taxClass = $taxClass;
        return $this;
    }

    /**
     * Get taxClass
     *
     * @return \XLite\Model\TaxClass 
     */
    public function getTaxClass()
    {
        return $this->taxClass;
    }

    /**
     * Set membership
     *
     * @param \XLite\Model\Membership $membership
     * @return Rate
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
