<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model;

/**
 * Order history events
 * todo: rename to OrderHistoryEvent
 *
 * @Entity
 * @Table (name="order_history_events")
 * @HasLifecycleCallbacks
 */
class OrderHistoryEvents extends \XLite\Model\AEntity
{
    /**
     * Order history event unique id
     *
     * @var mixed
     *
     * @Id
     * @GeneratedValue (strategy="AUTO")
     * @Column         (type="integer")
     */
    protected $event_id;

    /**
     * Event creation timestamp
     *
     * @var integer
     *
     * @Column (type="integer")
     */
    protected $date;

    /**
     * Code of event
     *
     * @var string
     *
     * @Column (type="string", length=255)
     */
    protected $code;

    /**
     * Human-readable description of event
     *
     * @var string
     *
     * @Column (type="string", length=1024, nullable=true)
     */
    protected $description;

    /**
     * Data for human-readable description
     *
     * @var string
     *
     * @Column (type="array")
     */
    protected $data;

    /**
     * Event comment
     *
     * @var string
     *
     * @Column (type="text")
     */
    protected $comment = '';

    /**
     * Event details
     *
     * @var \XLite\Model\OrderHistoryEventsData
     *
     * @OneToMany (targetEntity="XLite\Model\OrderHistoryEventsData", mappedBy="event", cascade={"all"})
     */
    protected $details;

    /**
     * Relation to a order entity
     *
     * @var \XLite\Model\Order
     *
     * @ManyToOne  (targetEntity="XLite\Model\Order", inversedBy="events", fetch="LAZY")
     * @JoinColumn (name="order_id", referencedColumnName="order_id", onDelete="CASCADE")
     */
    protected $order;

    /**
     * Author profile of the event
     *
     * @var \XLite\Model\Profile
     *
     * @ManyToOne   (targetEntity="XLite\Model\Profile", inversedBy="event", cascade={"merge","detach","persist"})
     * @JoinColumn (name="author_id", referencedColumnName="profile_id", onDelete="CASCADE")
     */
    protected $author;


    /**
     * Prepare order event before save data operation
     *
     * @return void
     *
     * @PrePersist
     * @PreUpdate
     */
    public function prepareBeforeSave()
    {
        if (!is_numeric($this->date)) {
            $this->setDate(\XLite\Core\Converter::time());
        }
    }

    /**
     * Description getter
     *
     * @return string
     */
    public function getDescription()
    {
        return static::t($this->description, (array)$this->getData());
    }

    /**
     * Details setter
     *
     * @param array $details Array of event details array($name => $value)
     *
     * @return void
     */
    public function setDetails(array $details)
    {
        foreach ($details as $detail) {
            $data = new \XLite\Model\OrderHistoryEventsData();
            $data->setName($detail['name']);
            $data->setValue($detail['value']);

            $this->addDetails($data);
            $data->setEvent($this);
        }
    }

    /**
     * Clone order and all related data
     *
     * @return \XLite\Model\OrderHistoryEvents
     */
    public function cloneEntity()
    {
        $entity = parent::cloneEntity();

        // Clone order details
        if ($this->getDetails()) {
            foreach ($this->getDetails() as $detail) {
                $cloned = $detail->cloneEntity();
                $entity->addDetails($cloned);
                $cloned->setEvent($entity);
            }
        }

        return $entity;
    }

    /**
     * Get event_id
     *
     * @return integer 
     */
    public function getEventId()
    {
        return $this->event_id;
    }

    /**
     * Set date
     *
     * @param integer $date
     * @return OrderHistoryEvents
     */
    public function setDate($date)
    {
        $this->date = $date;
        return $this;
    }

    /**
     * Get date
     *
     * @return integer 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set code
     *
     * @param string $code
     * @return OrderHistoryEvents
     */
    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }

    /**
     * Get code
     *
     * @return string 
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return OrderHistoryEvents
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Set data
     *
     * @param array $data
     * @return OrderHistoryEvents
     */
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * Get data
     *
     * @return array 
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set comment
     *
     * @param text $comment
     * @return OrderHistoryEvents
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
        return $this;
    }

    /**
     * Get comment
     *
     * @return text 
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Add details
     *
     * @param \XLite\Model\OrderHistoryEventsData $details
     * @return OrderHistoryEvents
     */
    public function addDetails(\XLite\Model\OrderHistoryEventsData $details)
    {
        $this->details[] = $details;
        return $this;
    }

    /**
     * Get details
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDetails()
    {
        return $this->details;
    }

    /**
     * Set order
     *
     * @param \XLite\Model\Order $order
     * @return OrderHistoryEvents
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

    /**
     * Set author
     *
     * @param \XLite\Model\Profile $author
     * @return OrderHistoryEvents
     */
    public function setAuthor(\XLite\Model\Profile $author = null)
    {
        $this->author = $author;
        return $this;
    }

    /**
     * Get author
     *
     * @return \XLite\Model\Profile 
     */
    public function getAuthor()
    {
        return $this->author;
    }
}
