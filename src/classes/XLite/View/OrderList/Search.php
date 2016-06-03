<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\OrderList;

/**
 * Orders search widget
 * TODO: reimplement with items lists usage
 *
 * @ListChild (list="orders.search.base", weight="30")
 */
class Search extends \XLite\View\OrderList\AOrderList
{
    /**
     * Widget class name
     *
     * @var string
     */
    protected $widgetClass = '\XLite\View\OrderList\Search';

    /**
     * Pager widget
     *
     * @var \XLite\View\Pager\Customer\Order\Search
     */
    protected $pager;

    /**
     * Search conditions (cache)
     *
     * @var array
     */
    protected $conditions = null;

    /**
     * Defines the JS files for the order search page
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'order/list/controller.js';

        return $list;
    }

    /**
     * Get orders
     *
     * @return array(\XLite\Model\Order)
     */
    public function getOrders(\XLite\Core\CommonCell $cnd = null)
    {
        if (!isset($this->orders)) {

            $this->orders = \XLite\Core\Database::getRepo('\XLite\Model\Order')
                ->search($this->getConditions($cnd));
        }

        return $this->orders;
    }

    /**
     * Get page data
     *
     * @return array
     */
    public function getPageData()
    {
        if (!isset($this->pager)) {
            $this->pager = $this->getWidget(
                array(\XLite\View\Pager\APager::PARAM_PAGE_ID => $this->getPageId()),
                '\XLite\View\Pager\Customer\Order\Search'
            );
        }

        return $this->getOrders($this->pager->getLimitCondition());
    }

    /**
     * Get page id
     *
     * @return integer
     */
    public function getPageId()
    {
        return abs(intval($this->getConditions()->pageId));
    }


    /**
     * Get profile
     *
     * @return \XLite\Model\Profile
     */
    protected function getProfile()
    {
        return \XLite\Core\Auth::getInstance()->getProfile(\XLite\Core\Request::getInstance()->profile_id);
    }

    /**
     * Get widget keys
     *
     * @return array
     */
    protected function getWidgetKeys()
    {
        return array(
            'mode' => 'search'
        );
    }

    /**
     * Get conditions
     *
     * @return array
     */
    protected function getConditions(\XLite\Core\CommonCell $cnd = null)
    {
        if (!isset($this->conditions)) {

            $this->conditions = \XLite\Core\Session::getInstance()->orders_search;

            if (!is_array($this->conditions)) {

                $this->conditions = array();

                \XLite\Core\Session::getInstance()->orders_search = $this->conditions;
            }

            foreach ($this->conditions as $key => $value) {

            }
        }

        $cnd = $cnd ?: new \XLite\Core\CommonCell();

        if ($this->getProfile()->isAdmin() && \XLite::isAdminZone()) {

            if (!empty(\XLite\Core\Request::getInstance()->profile_id)) {
                $cnd->profileId = \XLite\Core\Request::getInstance()->profile_id;
            }

        } else {
            $cnd->profileId = $this->getProfile()->getProfileId();
        }

        // Sort orders list in reverse chronological order by default
        if (!isset($this->conditions['sortCriterion']) || !$this->conditions['sortCriterion']) {
            $this->conditions['sortCriterion'] = 'order_id';
        }

        if (!isset($this->conditions['sortOrder']) || !$this->conditions['sortOrder']) {
            $this->conditions['sortOrder'] = 'DESC';
        }

        $cnd->orderBy = array('o.' . $this->conditions['sortCriterion'], $this->conditions['sortOrder']);

        if (isset($this->conditions['order_id'])) {
            $cnd->orderId = $this->conditions['order_id'];
        }

        if (isset($this->conditions['status'])) {
            $cnd->status = $this->conditions['status'];
        }

        $start = isset($this->conditions['startDate']) ? $this->conditions['startDate'] : 0;

        $end   = isset($this->conditions['endDate']) ? $this->conditions['endDate'] : 0;

        if ($start < $end) {
            $cnd->date = array($start, $end);
        }

        return $cnd;
    }
}
