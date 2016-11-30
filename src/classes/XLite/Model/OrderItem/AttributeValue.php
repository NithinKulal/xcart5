<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\OrderItem;

/**
 * Attribute value
 *
 * @Entity
 * @Table  (name="order_item_attribute_values")
 */
class AttributeValue extends \XLite\Model\AEntity
{
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
     * Order item (relation)
     *
     * @var \XLite\Model\OrderItem
     *
     * @ManyToOne  (targetEntity="XLite\Model\OrderItem", inversedBy="attributeValues")
     * @JoinColumn (name="item_id", referencedColumnName="item_id", onDelete="CASCADE")
     */
    protected $orderItem;

    /**
     * Name
     *
     * @var string
     *
     * @Column (type="string", length=255)
     */
    protected $name;

    /**
     * Value
     *
     * @var string
     *
     * @Column (type="string", length=255)
     */
    protected $value;

    /**
     * Attribute id
     *
     * @var integer
     *
     * @Column (type="integer", options={ "unsigned": true })
     */
    protected $attributeId = 0;

    /**
     * Attribute value (checkbox)
     *
     * @var \XLite\Model\AttributeValue\AttributeValueCheckbox
     *
     * @ManyToOne  (targetEntity="XLite\Model\AttributeValue\AttributeValueCheckbox")
     * @JoinColumn (name="attribute_value_checkbox_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $attributeValueC;

    /**
     * Attribute value (select)
     *
     * @var \XLite\Model\AttributeValue\AttributeValueSelect
     *
     * @ManyToOne  (targetEntity="XLite\Model\AttributeValue\AttributeValueSelect")
     * @JoinColumn (name="attribute_value_select_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $attributeValueS;

    /**
     * Attribute value (text)
     *
     * @var \XLite\Model\AttributeValue\AttributeValueText
     *
     * @ManyToOne  (targetEntity="XLite\Model\AttributeValue\AttributeValueText")
     * @JoinColumn (name="attribute_value_text_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $attributeValueT;

    /**
     * Set attribute value
     *
     * @var mixed $value Value
     *
     * @return void
     */
    public function setAttributeValue($value)
    {
        $method = 'setAttributeValue' . $value->getAttribute()->getType();
        if (method_exists($this, $method)) {
            $this->{$method}($value);
        }
    }

    /**
     * Get attribute value
     *
     * @return \XLite\Model\AttributeValue\AAttributeValue
     */
    public function getAttributeValue()
    {
        $value = null;

        if ($this->getAttributeValueS()) {
            $value = $this->getAttributeValueS();

        } elseif ($this->getAttributeValueC()) {
            $value = $this->getAttributeValueC();

        } elseif ($this->getAttributeValueT()) {
            $value = $this->getAttributeValueT();
        }

        return $value;
    }

    /**
     * Clone attribute value of order item
     *
     * @return static
     */
    public function cloneEntity()
    {
        /** @var static $new */
        $new = parent::cloneEntity();

        if ($this->getAttributeValue()) {
            $new->setAttributeValue($this->getAttributeValue());
        }

        return $new;
    }

    /**
     * Get order
     *
     * @return \XLite\Model\Order
     */
    public function getOrder()
    {
        return $this->getOrderItem()->getOrder();
    }

    /**
     * Get actual selected attribute name
     *
     * @return string
     */
    public function getActualName()
    {
        $attribute = null;

        if ($this->getAttributeValue()) {
            $attribute = $this->getAttributeValue()->getAttribute();
        }

        if (!$attribute
            && $this->getAttributeId()
        ) {
            /** @var \XLite\Model\Attribute $attribute */
            $attribute = \XLite\Core\Database::getRepo('\XLite\Model\Attribute')->find($this->getAttributeId());
        }

        /** @see \XLite\Model\AttributeTranslation */
        return $attribute
            ? $attribute->getName()
            : $this->getName();
    }

    /**
     * Get actual selected attribute value
     *
     * @return string
     */
    public function getActualValue()
    {
        $value = $this->getAttributeValue();

        return $value && !$value instanceof \XLite\Model\AttributeValue\AttributeValueText
            ? $value->asString()
            : $this->getValue();
    }

    /**
     * Get attribute value ID (selected option ID)
     *
     * @return integer
     */
    public function getAttributeValueId()
    {
        $value = $this->getAttributeValue();

        return $value && !$value instanceof \XLite\Model\AttributeValue\AttributeValueText
            ? $value->getId()
            : null;
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
     * Set name
     *
     * @param string $name
     * @return AttributeValue
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set value
     *
     * @param string $value
     * @return AttributeValue
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * Get value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set attributeId
     *
     * @param integer $attributeId
     * @return AttributeValue
     */
    public function setAttributeId($attributeId)
    {
        $this->attributeId = $attributeId;
        return $this;
    }

    /**
     * Get attributeId
     *
     * @return integer
     */
    public function getAttributeId()
    {
        return $this->attributeId;
    }

    /**
     * Set orderItem
     *
     * @param \XLite\Model\OrderItem $orderItem
     * @return AttributeValue
     */
    public function setOrderItem(\XLite\Model\OrderItem $orderItem = null)
    {
        $this->orderItem = $orderItem;
        return $this;
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

    /**
     * Set attributeValueC
     *
     * @param \XLite\Model\AttributeValue\AttributeValueCheckbox $attributeValueC
     * @return AttributeValue
     */
    public function setAttributeValueC(\XLite\Model\AttributeValue\AttributeValueCheckbox $attributeValueC = null)
    {
        $this->attributeValueC = $attributeValueC;
        return $this;
    }

    /**
     * Get attributeValueC
     *
     * @return \XLite\Model\AttributeValue\AttributeValueCheckbox
     */
    public function getAttributeValueC()
    {
        return $this->attributeValueC;
    }

    /**
     * Set attributeValueS
     *
     * @param \XLite\Model\AttributeValue\AttributeValueSelect $attributeValueS
     * @return AttributeValue
     */
    public function setAttributeValueS(\XLite\Model\AttributeValue\AttributeValueSelect $attributeValueS = null)
    {
        $this->attributeValueS = $attributeValueS;
        return $this;
    }

    /**
     * Get attributeValueS
     *
     * @return \XLite\Model\AttributeValue\AttributeValueSelect
     */
    public function getAttributeValueS()
    {
        return $this->attributeValueS;
    }

    /**
     * Set attributeValueT
     *
     * @param \XLite\Model\AttributeValue\AttributeValueText $attributeValueT
     * @return AttributeValue
     */
    public function setAttributeValueT(\XLite\Model\AttributeValue\AttributeValueText $attributeValueT = null)
    {
        $this->attributeValueT = $attributeValueT;
        return $this;
    }

    /**
     * Get attributeValueT
     *
     * @return \XLite\Model\AttributeValue\AttributeValueText
     */
    public function getAttributeValueT()
    {
        return $this->attributeValueT;
    }
}
