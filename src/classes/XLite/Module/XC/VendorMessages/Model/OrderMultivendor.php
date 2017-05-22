<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\VendorMessages\Model;

/**
 * Order
 *
 * @Decorator\After ("XC\VendorMessages")
 * @Decorator\Depend ("XC\MultiVendor")
 */
class OrderMultivendor extends \XLite\Model\Order implements \XLite\Base\IDecorator
{
    /**
     * Is watch messages
     *
     * @var boolean
     *
     * @Column (type="boolean")
     */
    protected $is_watch_messages = false;

    /**
     * Has opened disputes flag
     *
     * @var boolean
     *
     * @Column (type="boolean")
     */
    protected $is_opened_dispute = false;

    /**
     * Get watch messages flag
     *
     * @return boolean
     */
    public function getIsWatchMessages()
    {
        return $this->is_watch_messages;
    }

    /**
     * Set watch messages flag
     *
     * @param boolean $is_watch_messages Watch messages flag
     *
     * @return OrderMultivendor
     */
    public function setIsWatchMessages($is_watch_messages)
    {
        $this->is_watch_messages = $is_watch_messages;

        return $this;
    }

    /**
     * Get watch messages flag
     *
     * @return boolean
     */
    public function isWatchMessages()
    {
        return $this->getIsWatchMessages();
    }

    /**
     * Get opened dispute flag
     *
     * @return boolean
     */
    public function isOpenedDispute()
    {
        return $this->getIsOpenedDispute();
    }

    /**
     * Get opened dispute flag
     *
     * @return boolean
     */
    public function getIsOpenedDispute()
    {
        return $this->is_opened_dispute;
    }

    /**
     * Set opened dispute flag
     *
     * @param boolean $is_opened_dispute Opened dispute flag
     *
     * @return OrderMultivendor
     */
    public function setIsOpenedDispute($is_opened_dispute)
    {
        $this->is_opened_dispute = $is_opened_dispute;

        return $this;
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

        $targetOrders = array($this);

        if (\XLite\Module\XC\VendorMessages\Main::isWarehouse() && !$this->getParent()) {
            if ($profile->isVendor()) {
                foreach ($this->getChildren() as $order) {
                    if ($order->getVendor() && $order->getVendor()->getProfileId() == $profile->getProfileId()) {
                        $targetOrders = array($order);
                        break;
                    }
                }

            } else {
                foreach ($this->getChildren() as $order) {
                    $targetOrders[] = $order;
                }
            }

            $count = 0;
            foreach ($targetOrders as $order) {
                foreach ($order->getMessages() as $message) {
                    if (!$message->isRead($profile)) {
                        $count++;
                    }
                }
            }

        } else {
            $count = parent::countUnreadMessages($profile);
        }

        return $count;
    }

    /**
     * Count unread messages (for admin)
     *
     * @param \XLite\Model\Profile $profile Profile OPTIONAL
     *
     * @return integer
     */
    public function countUnreadMessagesForAdmin(\XLite\Model\Profile $profile = null)
    {
        $profile = $profile ?: \XLite\Core\Auth::getInstance()->getProfile();

        $targetOrders = array($this);

        if (\XLite\Module\XC\VendorMessages\Main::isWarehouse() && \XLite\Module\XC\VendorMessages\Main::isVendorAllowed() && !$this->getParent()) {
            if (!$this->getIsOpenedDispute()) {
                $targetOrders = array();
            }

            foreach ($this->getChildren() as $order) {
                if ($order->getIsOpenedDispute()) {
                    $targetOrders[] = $order;
                }
            }

            $count = 0;
            foreach ($targetOrders as $order) {
                foreach ($order->getMessages() as $message) {
                    if (!$message->isRead($profile)) {
                        $count++;
                    }
                }
            }

        } elseif (\XLite\Module\XC\VendorMessages\Main::isWarehouse() && \XLite\Module\XC\VendorMessages\Main::isVendorAllowed()) {
            $count = $this->getIsOpenedDispute() ? parent::countUnreadMessages($profile) : 0;

        } else {
            $count = parent::countUnreadMessages($profile);
        }

        return $count;
    }

    /**
     * Count unread messages (only own)
     *
     * @param \XLite\Model\Profile $profile Profile OPTIONAL
     *
     * @return integer
     */
    public function countOwnUnreadMessages(\XLite\Model\Profile $profile = null)
    {
        return parent::countUnreadMessages($profile);
    }


}
