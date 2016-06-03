<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Menu\Admin\LeftMenu\Marketplace;

/**
 * Node lazy load abstract class
 */
class LazyLoad extends \XLite\View\Menu\Admin\LeftMenu\ANodeLazyLoad
{
    /**
     * Returns default lazy class
     *
     * @return string
     */
    protected function getDefaultLazyClass()
    {
        return 'XLite\View\Menu\Admin\LeftMenu\Marketplace\Menu';
    }

    /**
     * Check read
     *
     * @return boolean
     */
    protected function isRead()
    {
        return !$this->getLazyWidget()->isUpdated();
    }

    /**
     * Add events to trigger node reloading
     *
     * @return string
     */
    protected function getLazyEvents()
    {
        $events = parent::getLazyEvents();
        $events[] = 'mp_event_get_XC5_notifications';

        return $events;
    }
}
