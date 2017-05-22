<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\VendorMessages\Model;

/**
 * Message
 *
 * @Entity
 * @Table (name="vendor_convo_message_reads")
 * @HasLifecycleCallbacks
 */
class MessageRead extends \XLite\Model\AEntity
{

    /**
     * Unique ID
     *
     * @var integer
     *
     * @Id
     * @GeneratedValue (strategy="AUTO")
     * @Column         (type="integer", options={ "unsigned": true })
     */
    protected $id;

    /**
     * Read date
     *
     * @var integer
     *
     * @Column (type="integer", options={ "unsigned": true })
     */
    protected $date;

    /**
     * Message
     *
     * @var \XLite\Module\XC\VendorMessages\Model\Message
     *
     * @ManyToOne  (targetEntity="XLite\Module\XC\VendorMessages\Model\Message", inversedBy="readers")
     * @JoinColumn (name="message_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $message;

    /**
     * Reader
     *
     * @var \XLite\Model\Profile
     *
     * @ManyToOne  (targetEntity="XLite\Model\Profile")
     * @JoinColumn (name="profile_id", referencedColumnName="profile_id", onDelete="CASCADE")
     */
    protected $reader;

    /**
     * Prepare date before create entity
     *
     * @PrePersist
     */
    public function prepareDate()
    {
        if (!$this->getDate()) {
            $this->setDate(\XLite\Core\Converter::time());
        }
    }

    /**
     * Get ID
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
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
     * Set date
     *
     * @param integer $date Set date
     *
     * @return MessageRead
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get message
     *
     * @return Message
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set message
     *
     * @param Message $message
     *
     * @return MessageRead
     */
    public function setMessage(Message $message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get reader
     *
     * @return \XLite\Model\Profile
     */
    public function getReader()
    {
        return $this->reader;
    }

    /**
     * Set reader
     *
     * @param \XLite\Model\Profile $reader Reader
     *
     * @return MessageRead
     */
    public function setReader(\XLite\Model\Profile $reader)
    {
        $this->reader = $reader;

        return $this;
    }

}