<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Menu\Admin\LeftMenu\Info;

/**
 * Left side menu widget
 */
class Menu extends \XLite\View\Menu\Admin\AAdmin
{
    /**
     * Returns CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        /** @var \XLite\View\Menu\Admin\LeftMenu\ANodeNotification $item */
        foreach ($this->getItems() as $item) {
            $list = array_merge($list, $item->getCSSFiles());
        }

        return $list;
    }

    /**
     * Returns JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        /** @var \XLite\View\Menu\Admin\LeftMenu\ANodeNotification $item */
        foreach ($this->getItems() as $item) {
            $list = array_merge($list, $item->getJSFiles());
        }

        return $list;
    }

    /**
     * Return unread count
     *
     * @return integer
     */
    public function isUpdated()
    {
        $isUpdated = false;

        /** @var \XLite\View\Menu\Admin\LeftMenu\ANodeNotification $item */
        foreach ($this->getItems() as $item) {
            $isUpdated = $item->isUpdated();

            if ($isUpdated) {
                break;
            }
        }

        return $isUpdated;
    }

    /**
     * Cache availability
     *
     * @return boolean
     */
    protected function isCacheAvailable()
    {
        return true;
    }

    /**
     * Get cache parameters
     *
     * @return array
     */
    protected function getCacheParameters()
    {
        $params = parent::getCacheParameters();
        $key = array();

        /** @var \XLite\View\Menu\Admin\LeftMenu\ANodeNotification $item */
        foreach ($this->getItems() as $item) {
            $key[] = $item->getCacheParameters();
        }

        $params[] = md5(serialize($key));

        return $params;
    }

    /**
     * Return widget directory
     *
     * @return string
     */
    protected function getDir()
    {
        return 'left_menu/info';
    }

    /**
     * Get default widget
     *
     * @return string
     */
    protected function getDefaultWidget()
    {
        return null;
    }

    /**
     * Define menu items
     *
     * @return array
     */
    protected function defineItems()
    {
        return array(
            'warning' => array(
                static::ITEM_WEIGHT     => 100,
                static::ITEM_WIDGET     => 'XLite\View\Menu\Admin\LeftMenu\Info\Warning',
            ),
            'lowStock' => array(
                static::ITEM_WEIGHT     => 200,
                static::ITEM_WIDGET     => 'XLite\View\Menu\Admin\LeftMenu\Info\LowStock',
            ),
            'upgrade' => array(
                static::ITEM_WEIGHT     => 300,
                static::ITEM_WIDGET     => 'XLite\View\Menu\Admin\LeftMenu\Info\Upgrade',
            )
        );
    }

    /**
     * Prepare items
     *
     * @param array $items Items
     *
     * @return array
     */
    protected function prepareItems($items)
    {
        uasort($items, array($this, 'sortItems'));
        foreach ($items as $index => $item) {
            $item[\XLite\View\Menu\Admin\LeftMenu\ANodeNotification::PARAM_LAST_READ] = $this->getLastReadTimestamp();

            $widget = isset($item[static::ITEM_WIDGET])
                ? $this->getWidget($item, $item[static::ITEM_WIDGET])
                : null;

            if ($widget
                && $widget->checkACL()
                && $widget->isVisible()
            ) {
                $items[$index] = $widget;

            } else {
                unset($items[$index]);
            }
        }

        return $items;
    }

    /**
     * Returns last read timestamp
     *
     * @return integer
     */
    protected function getLastReadTimestamp()
    {
        return \XLite\Core\TmpVars::getInstance()->infoMenuReadTimestamp;
    }

    /**
     * Get container tag attributes
     *
     * @return array
     */
    protected function getContainerTagAttributes()
    {
        $attributes = array(
            'class' => 'notification-menu',
        );

        $attributes['data-unread-count'] = $this->getUnreadCount();
        $attributes['data-menu-type'] = 'infoMenuReadTimestamp';

        return $attributes;
    }

    /**
     * Return unread count
     *
     * @return integer
     */
    protected function getUnreadCount()
    {
        $count = 0;
        /** @var \XLite\View\Menu\Admin\LeftMenu\ANodeNotification $item */
        foreach ($this->getItems() as $item) {
            $count += $item->getUnreadCount();
        }

        return $count;
    }
}
