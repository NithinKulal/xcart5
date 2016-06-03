<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Egoods\View\Order;

/**
 * Order egoods list 
 */
class Egoods extends \XLite\View\AView
{
    /**
     * Items 
     * 
     * @var   array
     */
    protected $items;

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/CDev/Egoods/order_egoods.css';

        return $list;
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && $this->getItems();
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/CDev/Egoods/order_egoods.twig';
    }

    /**
     * Get order items with attachments
     * 
     * @return array
     */
    protected function getItems()
    {
        if (!isset($this->items)) {
            $this->items = array();
            foreach ($this->getOrder()->getItems() as $item) {
                if (0 < count($item->getPrivateAttachments())) {
                    $this->items[] = $item;
                }
            }
        }

        return $this->items;
    }

    /**
     * Get attachment item list class 
     * 
     * @param integer                                                     $index      Attachment index
     * @param \XLite\Module\CDev\Egoods\Model\OrderItem\PrivateAttachment $attachment Attachment
     *  
     * @return string
     */
    protected function getAttachmentClass($index, \XLite\Module\CDev\Egoods\Model\OrderItem\PrivateAttachment $attachment)
    {
        $classes = array();

        if (0 == $index % 3) {
            $classes[] = 'last-row';
        }

        $classes[] = $attachment->getAttachment() ? 'live-attachment' : 'deleted-attachment';

        return implode(' ', $classes);
    }

    /**
     * Format TTL 
     * 
     * @param integer $ttl TTLT in seconds
     *  
     * @return string
     */
    protected function formatTTL($ttl)
    {
        if (3600 > $ttl) {
            $label = static::t('less one hour');

        } elseif (86400 > $ttl) {
            $label = static::t('X hours', array('hours' => floor($ttl / 3600)));

        } else {
            $label = static::t('X days', array('days' => floor($ttl / 86400)));

        }

        return $label;
    }

    /**
     * Get status class 
     * 
     * @param \XLite\Module\CDev\Egoods\Model\OrderItem\PrivateAttachment $attachment Attachment
     *  
     * @return string
     */
    protected function getStatusClass(\XLite\Module\CDev\Egoods\Model\OrderItem\PrivateAttachment $attachment)
    {
        if ($attachment->isExpired() || $attachment->isAttemptsEnded()) {
            $class = 'expired';

        } elseif ($attachment->getBlocked() || !$attachment->isAvailable()) {
            $class = 'blocked';

        } else {
            $class = 'avail';
        }

        return $class;
    }
}
