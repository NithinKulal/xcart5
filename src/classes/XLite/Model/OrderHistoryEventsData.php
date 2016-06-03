<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model;

/**
 * Order history event data 
 *
 * @Entity
 * @Table (name="order_history_event_data",
 *      indexes={
 *          @Index (name="en", columns={"event_id","name"})
 *      }
 * )
 */
class OrderHistoryEventsData extends \XLite\Model\AEntity
{
    /**
     * Primary key
     *
     * @var integer
     *
     * @Id
     * @GeneratedValue (strategy="AUTO")
     * @Column         (type="integer")
     */
    protected $id;

    /**
     * Record name
     *
     * @var string
     *
     * @Column (type="string", length=128)
     */
    protected $name;

    /**
     * Value
     *
     * @var string
     *
     * @Column (type="text")
     */
    protected $value;

    /**
     * Transaction
     *
     * @var \XLite\Model\Payment\Transaction
     *
     * @ManyToOne  (targetEntity="XLite\Model\OrderHistoryEvents", inversedBy="details")
     * @JoinColumn (name="event_id", referencedColumnName="event_id", onDelete="CASCADE")
     */
    protected $event;


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
     * @return OrderHistoryEventsData
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
     * @param text $value
     * @return OrderHistoryEventsData
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
     * Set event
     *
     * @param \XLite\Model\OrderHistoryEvents $event
     * @return OrderHistoryEventsData
     */
    public function setEvent(\XLite\Model\OrderHistoryEvents $event = null)
    {
        $this->event = $event;
        return $this;
    }

    /**
     * Get event
     *
     * @return \XLite\Model\OrderHistoryEvents 
     */
    public function getEvent()
    {
        return $this->event;
    }
}
