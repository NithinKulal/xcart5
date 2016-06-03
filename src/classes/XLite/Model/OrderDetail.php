<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model;

/**
 * Order details
 *
 * @Entity
 * @Table (name="order_details",
 *      indexes={
 *          @Index (name="oname", columns={"order_id","name"})
 *      }
 * )
 */
class OrderDetail extends \XLite\Model\AEntity
{
    /**
     * Order detail unique id
     *
     * @var mixed
     *
     * @Id
     * @GeneratedValue (strategy="AUTO")
     * @Column         (type="integer")
     */
    protected $detail_id;

    /**
     * Record name (code)
     *
     * @var string
     *
     * @Column (type="string", length=255)
     */
    protected $name;

    /**
     * Record label
     *
     * @var string
     *
     * @Column (type="string", length=255, nullable=true)
     */
    protected $label;

    /**
     * Value
     *
     * @var string
     *
     * @Column (type="text")
     */
    protected $value;

    /**
     * Relation to a order entity
     *
     * @var \XLite\Model\Order
     *
     * @ManyToOne  (targetEntity="XLite\Model\Order", inversedBy="details", fetch="LAZY")
     * @JoinColumn (name="order_id", referencedColumnName="order_id", onDelete="CASCADE")
     */
    protected $order;

    /**
     * Get display record name
     *
     * @return string
     */
    public function getDisplayName()
    {
        return $this->getLabel() ?: $this->getName();
    }

    /**
     * Get detail_id
     *
     * @return integer 
     */
    public function getDetailId()
    {
        return $this->detail_id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return OrderDetail
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
     * Set label
     *
     * @param string $label
     * @return OrderDetail
     */
    public function setLabel($label)
    {
        $this->label = $label;
        return $this;
    }

    /**
     * Get label
     *
     * @return string 
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Set value
     *
     * @param text $value
     * @return OrderDetail
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * Get value
     *
     * @return text 
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set order
     *
     * @param \XLite\Model\Order $order
     * @return OrderDetail
     */
    public function setOrder(\XLite\Model\Order $order = null)
    {
        $this->order = $order;
        return $this;
    }

    /**
     * Get order
     *
     * @return \XLite\Model\Order 
     */
    public function getOrder()
    {
        return $this->order;
    }
}
