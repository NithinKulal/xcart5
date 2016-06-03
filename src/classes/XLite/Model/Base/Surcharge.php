<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Base;

/**
 * Surcharge
 *
 * @MappedSuperclass
 */
abstract class Surcharge extends \XLite\Model\AEntity
{
    /**
     * Surcharge type codes
     */
    const TYPE_TAX      = 'tax';
    const TYPE_DISCOUNT = 'discount';
    const TYPE_SHIPPING = 'shipping';
    const TYPE_HANDLING = 'handling';


    /**
     * Type names
     *
     * @var array
     */
    protected static $typeNames = array(
        self::TYPE_TAX      => 'Tax cost',
        self::TYPE_DISCOUNT => 'Discount',
        self::TYPE_SHIPPING => 'Shipping cost',
        self::TYPE_HANDLING => 'Handling cost',
    );

    /**
     * ID
     *
     * @var integer
     *
     * @Id
     * @GeneratedValue (strategy="AUTO")
     * @Column         (type="integer", options={ "unsigned": true })
     */
    protected $id;

    /**
     * Type
     *
     * @var string
     *
     * @Column (type="string", options={ "fixed": true }, length=8)
     */
    protected $type;

    /**
     * Code
     *
     * @var string
     *
     * @Column (type="string", length=128)
     */
    protected $code;

    /**
     * Control class name
     *
     * @var string
     *
     * @Column (type="string", length=255)
     */
    protected $class;

    /**
     * Surcharge include flag
     *
     * @var boolean
     *
     * @Column (type="boolean")
     */
    protected $include = false;

    /**
     * Surcharge evailability
     *
     * @var boolean
     *
     * @Column (type="boolean")
     */
    protected $available = true;

    /**
     * Value
     *
     * @var float
     *
     * @Column (type="decimal", precision=14, scale=4)
     */
    protected $value;

    /**
     * Name (stored)
     *
     * @var string
     *
     * @Column (type="string", length=255)
     */
    protected $name;

    /**
     * Weight
     *
     * @var integer
     *
     * @Column (type="integer")
     */
    protected $weight = 0;

    /**
     * Get order
     *
     * @return \XLite\Model\Order
     */
    abstract public function getOrder();

    /**
     * Set owner
     *
     * @param \XLite\Model\Base\SurchargeOwner $owner Owner
     *
     * @return \XLite\Model\Base\Surcharge
     */
    public function setOwner(\XLite\Model\Base\SurchargeOwner $owner = null)
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * Get unque surcharge key
     *
     * @return string
     */
    public function getKey()
    {
        return $this->getType() . $this->getClass() . $this->name;
    }

    /**
     * Get modifier
     *
     * @return \XLite\Model\Order\Modifier
     */
    public function getModifier()
    {
        $found = null;

        foreach ($this->getOrder()->getModifiers() as $modifier) {
            if ($modifier->isSurchargeOwner($this)) {
                $found = $modifier;
                break;
            }
        }

        return $found;
    }

    /**
     * Get surcharge info
     *
     * @return \XLite\DataSet\Transport\Surcharge
     */
    public function getInfo()
    {
        $modifier = $this->getModifier();

        return $modifier
            ? $modifier->getSurchargeInfo($this)
            : null;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        $info = $this->getInfo();

        return $info && $info->name ? $info->name : $this->name;
    }

    /**
     * Get type name
     *
     * @return string
     */
    public function getTypeName()
    {
        return isset(static::$typeNames[$this->getType()])
            ? \XLite\Core\Translation::getInstance()->translate(static::$typeNames[$this->getType()])
            : null;
    }

    /**
     * Set value
     *
     * @param float $value Value
     *
     * @return void
     */
    public function setValue($value)
    {
        $this->value = round($value, \XLite\Logic\Math::STORE_PRECISION);
    }

    /**
     * Check - current and specified surcharges are equal or not
     *
     * @param \XLite\Model\Base\Surcharge $surcharge Another surcharge
     *
     * @return boolean
     */
    public function isEqualSurcharge(\XLite\Model\Base\Surcharge $surcharge)
    {
        return $this->getCode() === $surcharge->getCode()
            && $this->getType() === $surcharge->getType()
            && $this->getInclude() === $surcharge->getInclude()
            && $this->getClass() === $surcharge->getClass();
    }

    /**
     * Replace surcharge
     *
     * @param \XLite\Model\Base\Surcharge $surcharge Surcharge for replacing
     *
     * @return void
     */
    public function replaceSurcharge(\XLite\Model\Base\Surcharge $surcharge)
    {
        $this->map($surcharge->getReplacedProperties());

        $owner = $surcharge->getOwner();

        $owner->removeSurcharge($surcharge);
        $owner->addSurcharges($this);
        $this->setOwner($owner);
    }

    /**
     * Get replaced properties
     *
     * @return array
     */
    public function getReplacedProperties()
    {
        return array(
            'value'     => $this->getValue(),
            'available' => $this->getAvailable(),
        );
    }

    /**
     * Returns display sorting weight
     *
     * @return integer
     */
    public function getSortingWeight()
    {
        return null !== $this->getModifier()
            ? $this->getModifier()->getSortingWeight()
            : $this->weight;
    }
}
