<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\VendorMessages\Model;

/**
 * Order
 */
class Order extends \XLite\Model\Order implements \XLite\Base\IDecorator
{
    /**
     * Messages
     *
     * @var \Doctrine\Common\Collections\Collection
     *
     * @OneToMany (targetEntity="XLite\Module\XC\VendorMessages\Model\Message", mappedBy="order", cascade={"all"})
     * @OrderBy   ({"date" = "ASC"})
     */
    protected $messages;

    /**
     * @inheritdoc
     */
    public function __construct(array $data = array())
    {
        parent::__construct($data);

        $this->messages = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get messages
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * Set messages
     *
     * @param \Doctrine\Common\Collections\Collection $messages Messages
     *
     * @return static
     */
    public function setMessages(\Doctrine\Common\Collections\Collection $messages)
    {
        $this->messages = $messages;

        return $this;
    }

    /**
     * Set messages
     *
     * @param \XLite\Module\XC\VendorMessages\Model\Message $message Message
     *
     * @return static
     */
    public function addMessages(\XLite\Module\XC\VendorMessages\Model\Message $message)
    {
        $this->messages->add($message);

        return $this;
    }

    /**
     * Build new message
     *
     * @return \XLite\Module\XC\VendorMessages\Model\Message
     */
    public function buildNewMessage()
    {
        $message = new \XLite\Module\XC\VendorMessages\Model\Message();
        $message->setOrder($this);
        $this->addMessages($message);

        $author = \XLite\Core\Auth::getInstance()->getProfile() ?: $this->getProfile();
        $message->setAuthor($author);

        $message->resetNotifications();

        $message->markAsRead($author);

        return $message;
    }

    /**
     * Get last message
     *
     * @return \XLite\Module\XC\VendorMessages\Model\Message
     */
    public function getLastMessage()
    {
        return count($this->getMessages()) > 0 ? $this->getMessages()->last() : null;
    }

    /**
     * Count unread messages
     *
     * @param \XLite\Model\Profile $profile Profile OPTIONAL
     *
     * @return integer
     */
    public function countUnreadMessages(\XLite\Model\Profile $profile = null)
    {
        $profile = $profile ?: \XLite\Core\Auth::getInstance()->getProfile();

        $count = 0;
        foreach ($this->getMessages() as $message) {
            if (!$message->isRead($profile)) {
                $count++;
            }
        }

        return $count;
    }

}