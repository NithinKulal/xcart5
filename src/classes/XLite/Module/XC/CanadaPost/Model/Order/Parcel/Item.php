<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CanadaPost\Model\Order\Parcel;

/**
 * Class represents a Canada Post parcel items
 *
 * @Entity
 * @Table  (name="order_capost_parcel_items")
 */
class Item extends \XLite\Model\AEntity
{
    /**
     * Item unique ID
     *
     * @var integer
     *
     * @Id
     * @GeneratedValue (strategy="AUTO")
     * @Column         (type="integer")
     */
	protected $id;

    /**
     * Item's parcel (reference to the canada post parcels model)
     *
     * @var \XLite\Module\XC\CanadaPost\Model\Order\Parcel
     *
     * @ManyToOne  (targetEntity="XLite\Module\XC\CanadaPost\Model\Order\Parcel", inversedBy="items")
     * @JoinColumn (name="parcelId", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $parcel;

	/**
	 * Item's order item (reference to the order items model)
	 *
	 * @var \XLite\Model\OrderItem 
	 *
	 * @ManyToOne  (targetEntity="XLite\Model\OrderItem", inversedBy="capostParcelItems")
	 * @JoinColumn (name="orderItemId", referencedColumnName="item_id", onDelete="CASCADE")
	 */
	protected $orderItem;

    /**
     * Item quantity
     *
     * @var integer
     *
     * @Column (type="integer")
     */
    protected $amount = 0;

	// {{{ Service methods

    /**
     * Assign the parcel
     *
     * @param \XLite\Module\XC\CanadaPost\Model\Order\Parcel $parcel Order's parcel (OPTIONAL)
     *
     * @return void
     */
    public function setParcel(\XLite\Module\XC\CanadaPost\Model\Order\Parcel $parcel = null)
    {
        $this->parcel = $parcel;
    }

    /**
     * Assign the order item
     *
     * @param \XLite\Model\OrderItem $orderItem Order's item (OPTIONAL)
     *
     * @return void
     */
    public function setOrderItem(\XLite\Model\OrderItem $orderItem = null)
    {
        $this->orderItem = $orderItem;
    }

	// }}}

    /**
     * Get single item weight (in store weight units)
     *
     * @return float
     */
    public function getWeight()
    {
        $object = $this->getOrderItem()->getObject();
        $result = ($object) ? $object->getWeight() : 0;

        foreach ($this->getOrderItem()->getAttributeValues() as $attributeValue) {
            if ($attributeValue->getAttributeValue()) {
                $result += $attributeValue->getAttributeValue()->getAbsoluteValue('weight');
            }
        }

        return $result;
    }

    /**
     * Get single item weight in KG
     *
     * @param boolean $adjustFloatValue Flag - adjust float value or not (OPTIONAL)
     *
     * @return float
     */
    public function getWeightInKg($adjustFloatValue = false)
    {
        // Convert weight from store units to KG (weight must be in KG)
        $weight = \XLite\Core\Converter::convertWeightUnits(
            $this->getWeight(),
            \XLite\Core\Config::getInstance()->Units->weight_unit,
            'kg'
        );

        if ($adjustFloatValue) {
            // Adjust according to the XML element schema
            $weight = \XLite\Module\XC\CanadaPost\Core\Service\AService::adjustFloatValue($weight, 3, 0, 99.999);
        }

        return $weight;
    }

    /**
	 * Get total item weight (in store weight units)
	 *
	 * @return float
	 */
	public function getTotalWeight()
	{
		return $this->getWeight() * $this->getAmount();
	}

    /**
     * Get total item weight in KG
     *
     * @param boolean $adjustFloatValue Flag - adjust float value or not (OPTIONAL)
     *
     * @return float
     */
    public function getTotalWeightInKg($adjustFloatValue = false)
    {
        return $this->getWeightInKg($adjustFloatValue) * $this->getAmount();
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
     * Set amount
     *
     * @param integer $amount
     * @return Item
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
        return $this;
    }

    /**
     * Get amount
     *
     * @return integer 
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Get parcel
     *
     * @return \XLite\Module\XC\CanadaPost\Model\Order\Parcel 
     */
    public function getParcel()
    {
        return $this->parcel;
    }

    /**
     * Get orderItem
     *
     * @return \XLite\Model\OrderItem 
     */
    public function getOrderItem()
    {
        return $this->orderItem;
    }
}
