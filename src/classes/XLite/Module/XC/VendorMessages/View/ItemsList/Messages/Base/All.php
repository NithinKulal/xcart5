<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\VendorMessages\View\ItemsList\Messages\Base;

/**
 * All messages
 */
abstract class All extends \XLite\View\ItemsList\AItemsList
{

    /**
     * @inheritdoc
     */
    static public function getSearchParams()
    {
        return array();
    }

    /**
     * @inheritdoc
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = $this->getPageBodyDir() . '/style.css';

        return $list;
    }

    /**
     * @inheritdoc
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = $this->getPageBodyDir() . '/controller.js';

        return $list;
    }

    /**
     * @inheritdoc
     */
    public function getListCSSClasses()
    {
        return parent::getListCSSClasses() . ' all-messages';
    }

    /**
     * @inheritdoc
     */
    protected function isPagerVisible()
    {
        return parent::isPagerVisible()
            && $this->getItemsCount() > 0;
    }

    /**
     * @inheritdoc
     */
    protected function getPageBodyDir()
    {
        return 'modules/XC/VendorMessages/items_list/messages/all';
    }

    /**
     * @inheritdoc
     */
    protected function getSearchCondition()
    {
        $condition = parent::getSearchCondition();

        foreach (static::getSearchParams() as $modelParam => $requestParam) {
            $paramValue = $this->getParam($requestParam);

            if ('' !== $paramValue && 0 !== $paramValue) {
                $condition->$modelParam = $paramValue;
            }
        }

        $condition->{\XLite\Model\Repo\Order::P_ORDER_BY} = array(
            'read_messages',
            'asc',
            \XLite\Core\Auth::getInstance()->getProfile(),
        );

        return $condition;
    }

    /**
     * @inheritdoc
     */
    protected function getData(\XLite\Core\CommonCell $cnd, $countOnly = false)
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Order')->search($cnd, $countOnly);
    }

    /**
     * @inheritdoc
     */
    protected function isDisplayWithEmptyList()
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    protected function getPageBodyTemplate()
    {
        return $this->getPageBodyDir() . LC_DS . $this->getPageBodyFile();
    }

    /**
     * @inheritdoc
     */
    protected function getEmptyListTemplate()
    {
        return $this->getPageBodyDir() . LC_DS . $this->getEmptyListFile();
    }

    // {{{ Content helpers

    /**
     * Get order's last message
     *
     * @param \XLite\Model\Order $order Order
     *
     * @return \XLite\Module\XC\VendorMessages\Model\Message
     */
    protected function getLastMessage(\XLite\Model\Order $order)
    {
        return $order->getLastMessage();
    }

    /**
     * Get message line tag attributes
     *
     * @param \XLite\Model\Order $order Order
     *
     * @return array
     */
    protected function getLineTagAttributes(\XLite\Model\Order $order)
    {
        $message = $this->getLastMessage($order);

        $attributes = array(
            'class' => array('message'),
        );

        $attributes['class'][] = !$message || $message->isRead() ? 'read' : 'unread';

        return $attributes;
    }

    /**
     * Prepare body
     *
     * @param \XLite\Model\Order $order Order
     *
     * @return string
     */
    protected function prepareBody(\XLite\Model\Order $order)
    {
        $message = $this->getLastMessage($order);

        return $message ? $message->getBody() : static::t('n/a');
    }

    /**
     * Prepare time
     *
     * @param \XLite\Model\Order $order Order
     *
     * @return string
     */
    protected function prepareTime(\XLite\Model\Order $order)
    {
        $message = $this->getLastMessage($order);

        return $message ? $this->formatTime($message->getDate()) : static::t('n/a');
    }

    /**
     * Get row label
     *
     * @param \XLite\Model\Order $order Order
     *
     * @return string
     */
    protected function getLabel(\XLite\Model\Order $order)
    {
        $count = $order->countUnreadMessages();

        return $count > 1
            ? static::t('X new message for order', array('count' => $count))
            : static::t('New message for order');
    }

    /**
     * Check - conversation marks visible or not
     *
     * @param \XLite\Model\Order $order Order
     *
     * @return boolean
     */
    protected function isMarksVisible(\XLite\Model\Order $order)
    {
        return false;
    }

    // }}}
}
