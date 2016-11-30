<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

/**
 * Orders list controller
 */
class RecentOrders extends \XLite\Controller\Admin\OrderList
{
    /**
     * Get itemsList class
     *
     * @return string
     */
    public function getItemsListClass()
    {
        return \XLite\Core\Request::getInstance()->itemsList
            ?: 'XLite\View\ItemsList\Model\Order\Admin\Recent';
    }

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Orders awaiting processing');
    }

    /**
     * Handles the request.
     *
     * @return void
     */
    public function handleRequest()
    {
        \XLite\Core\Session::getInstance()->{$this->getSessionCellName()} = array(
            \XLite\Model\Repo\Order::P_DATE => array(LC_START_TIME - 86400, LC_START_TIME),
        );

        parent::handleRequest();
    }
}
