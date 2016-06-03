<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Menu\Admin\LeftMenu\Info;

/**
 * Node lazy load abstract class
 */
class Warning extends \XLite\View\Menu\Admin\LeftMenu\ANodeNotification
{
    /**
     * Messages
     *
     * @var array
     */
    protected $messages = null;

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible() && $this->getMessages();
    }

    /**
     * Check if content is updated
     *
     * @return boolean
     */
    public function isUpdated()
    {
        return $this->getLastReadTimestamp() < $this->getLastUpdateTimestamp();
    }

    /**
     * Returns count of unread messages
     *
     * @return integer
     */
    public function getUnreadCount()
    {
        return array_reduce($this->getMessages(), array($this, 'countMessages'), 0);
    }

    /**
     * Get cache parameters
     *
     * @return array
     */
    public function getCacheParameters()
    {
        return array();
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'left_menu/info/warning.twig';
    }

    /**
     * Get urgent messages
     *
     * @return array
     */
    protected function getMessages()
    {
        if (!isset($this->messages)) {
            $this->messages = array_map(array($this, 'parseMessage'), $this->fetchMessages());
            usort($this->messages, array($this, 'sortMessages'));

            $this->setLastUpdateTimestamp(array_reduce($this->messages, array($this, 'maxDate'), 0));
        }

        return $this->messages;
    }

    /**
     * Fetch messages
     *
     * @return array
     */
    protected function fetchMessages()
    {
        $result = array();
        $messages = \XLite\Core\Marketplace::getInstance()->getXC5Notifications();

        if ($messages) {
            foreach ($messages as $message) {
                if ($message['type'] == 'warning') {
                    $result[] = $message;
                }
            }
        }

        return $result;
    }

    /**
     * Return update timestamp
     *
     * @return integer
     */
    protected function getLastUpdateTimestamp()
    {
        $result = \XLite\Core\TmpVars::getInstance()->warningMessageLastTimestamp;

        if (!isset($result)) {
            $result = LC_START_TIME;
            \XLite\Core\TmpVars::getInstance()->warningMessageLastTimestamp = $result;
        }

        return $result;
    }

    /**
     * Set update timestamp
     *
     * @param integer $timestamp Timestamp
     *
     * @return void
     */
    protected function setLastUpdateTimestamp($timestamp)
    {
        \XLite\Core\TmpVars::getInstance()->warningMessageLastTimestamp = $timestamp;
    }

    /**
     * Parse message
     *
     * @param \SimpleXMLElement $message Message
     *
     * @return array
     */
    protected function parseMessage($message)
    {
        if ($message['link']) {
            $message['link'] = $message['link']
                . (strpos($message['link'], '?') === false ? '?' : '&')
                . 'utm_source=xc5admin&utm_medium=link2blog&utm_campaign=xc5adminlink2blog';
        }

        return $message;
    }

    /**
     * Sort helper
     *
     * @param array $a First message
     * @param array $b Second message
     *
     * @return boolean
     */
    protected function sortMessages($a, $b)
    {
        return isset($a['date'])
            && isset($b['date'])
            && $a['date'] < $b['date'];
    }

    /**
     * Count helper
     *
     * @param integer $carry Carry
     * @param array   $item  Message
     *
     * @return integer
     */
    protected function countMessages($carry, $item)
    {
        if ($item['date'] >= $this->getLastReadTimestamp()) {
            $carry += 1;
        }

        return $carry;
    }

    /**
     * Max date helper
     *
     * @param integer $carry Carry
     * @param array   $item  Message
     *
     * @return integer
     */
    protected function maxDate($carry, $item)
    {
        return max($carry, $item['date']);
    }

    // {{{ View helpers

    /**
     * Returns node style class
     *
     * @return array
     */
    protected function getNodeStyleClasses()
    {
        $list = parent::getNodeStyleClasses();
        $list[] = 'warning';

        return $list;
    }

    /**
     * Returns icon
     *
     * @return string
     */
    protected function getIcon()
    {
        return $this->getSVGImage('images/warning.svg');
    }

    /**
     * Returns header
     *
     * @return string
     */
    protected function getHeader()
    {
        return static::t('Security issue');
    }

    // }}}
}
