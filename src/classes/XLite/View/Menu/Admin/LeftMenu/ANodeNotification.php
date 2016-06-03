<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Menu\Admin\LeftMenu;

/**
 * Notification node abstract class
 */
abstract class ANodeNotification extends \XLite\View\AView
{
    /**
     * Widget params
     */
    const PARAM_LAST_READ = 'lastReadTimestamp';

    /**
     * Check if data is updated (must be fast)
     *
     * @return boolean
     */
    abstract public function isUpdated();

    /**
     * Returns count of unread messages
     *
     * @return integer
     */
    public function getUnreadCount()
    {
        return $this->isUpdated() ? 1 : 0;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'left_menu/node_notification.twig';
    }

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            self::PARAM_LAST_READ => new \XLite\Model\WidgetParam\TypeInt('Last read timestamp', 0),
        );
    }

    /**
     * Last read timestamp
     *
     * @return integer
     */
    protected function getLastReadTimestamp()
    {
        return $this->getParam(static::PARAM_LAST_READ);
    }

    // {{{ View helpers

    /**
     * Returns node tag attribute
     *
     * @return array
     */
    protected function getNodeTagAttributes()
    {
        $result['class'] = implode(' ', $this->getNodeStyleClasses());

        return $result;
    }

    /**
     * Returns node style class
     *
     * @return array
     */
    protected function getNodeStyleClasses()
    {
        $list = array('notification-item');

        if ($this->isUpdated()) {
            $list[] = 'updated';
        }

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
     * Returns header url
     *
     * @return string
     */
    protected function getHeaderUrl()
    {
        return '';
    }

    /**
     * Returns header
     *
     * @return string
     */
    protected function getHeader()
    {
        return '';
    }

    /**
     * Get entries count
     *
     * @return integer
     */
    protected function getCounter()
    {
        return 0;
    }

    // }}}
}
