<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Menu\Admin\LeftMenu;

/**
 * Node lazy load abstract class
 */
abstract class ANodeLazyLoad extends \XLite\View\Base\ALazyLoad
{
    /**
     * Check read
     *
     * @return boolean
     */
    abstract protected function isRead();

    /**
     * Returns CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'left_menu/notifications.css';

        $list = array_merge($list, $this->getLazyWidget()->getCSSFiles());

        return $list;
    }

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'left_menu/notifications.js';

        $list = array_merge($list, $this->getLazyWidget()->getJSFiles());

        return $list;
    }

    /**
     * Returns style classes
     *
     * @return array
     */
    protected function getStyleClasses()
    {
        $list = parent::getStyleClasses();
        $list[] = 'box';

        if ($this->isRead()) {
            $list[] = 'read';
        }

        return $list;
    }
}
