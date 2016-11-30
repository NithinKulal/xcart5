<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\ItemsList\Order\Customer;

use XLite\View\ItemsList\Order\AOrder;

/**
 * Search
 */
class Search extends AOrder
{
    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list   = parent::getCSSFiles();
        $list[] = 'items_list/order/style.css';

        return $list;
    }

    /**
     * Defines the JS files for the order search page
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'items_list/order/controller.js';

        return $list;
    }

    /**
     * @return string
     */
    protected function getListHead()
    {
        return $this->getItemsCount()
            ? static::t('X orders', ['count' => $this->getItemsCount()])
            : static::t('No orders');
    }

    /**
     * @return boolean
     */
    protected function isHeadVisible()
    {
        return true;
    }

    /**
     * Return class name for the list pager
     *
     * @return string
     */
    protected function getPagerClass()
    {
        return 'XLite\View\Pager\Customer\Order\Search';
    }

    /**
     * @param  \XLite\Core\CommonCell $searchCase Search case
     *
     * @return \XLite\Core\CommonCell
     */
    protected function postprocessSearchCase(\XLite\Core\CommonCell $searchCase)
    {
        $searchCase = parent::postprocessSearchCase($searchCase);

        $searchCase->{\XLite\Model\Repo\Order::P_PROFILE_ID} =
            \XLite\Core\Auth::getInstance()->getProfile()->getProfileId();

        return $searchCase;
    }

    /**
     * getSortOrderDefault
     *
     * @return string
     */
    protected function getSortOrderModeDefault()
    {
        return static::SORT_ORDER_DESC;
    }

    /**
     * Check if pager is visible
     *
     * @return boolean
     */
    protected function isPagerVisible()
    {
        return parent::isPagerVisible() && $this->hasResults();
    }

    /**
     * Auxiliary method to check visibility
     *
     * @return boolean
     */
    protected function isDisplayWithEmptyList()
    {
        return true;
    }

    /**
     * getSortByModeDefault
     *
     * @return string
     */
    protected function getSortByModeDefault()
    {
        return 'o.order_id';
    }
}
