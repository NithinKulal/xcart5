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
class OrderList extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Check ACL permissions
     *
     * @return boolean
     */
    public function checkACL()
    {
        return parent::checkACL() || \XLite\Core\Auth::getInstance()->isPermissionAllowed('manage orders');
    }

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Orders');
    }

    // {{{ Search

    /**
     * Get itemsList class
     *
     * @return string
     */
    public function getItemsListClass()
    {
        return \XLite\Core\Request::getInstance()->itemsList
            ?: '\XLite\View\ItemsList\Model\Order\Admin\Search';
    }

    /**
     * getDateValue
     * FIXME - to remove
     *
     * @param string  $fieldName Field name (prefix)
     * @param boolean $isEndDate End date flag OPTIONAL
     *
     * @return integer
     */
    public function getDateValue($fieldName, $isEndDate = false)
    {
        $dateValue = \XLite\Core\Request::getInstance()->$fieldName;

        if (!isset($dateValue)) {
            $nameDay   = $fieldName . 'Day';
            $nameMonth = $fieldName . 'Month';
            $nameYear  = $fieldName . 'Year';

            if (
                isset(\XLite\Core\Request::getInstance()->$nameMonth)
                && isset(\XLite\Core\Request::getInstance()->$nameDay)
                && isset(\XLite\Core\Request::getInstance()->$nameYear)
            ) {
                $dateValue = mktime(
                    $isEndDate ? 23 : 0,
                    $isEndDate ? 59 : 0,
                    $isEndDate ? 59 : 0,
                    \XLite\Core\Request::getInstance()->$nameMonth,
                    \XLite\Core\Request::getInstance()->$nameDay,
                    \XLite\Core\Request::getInstance()->$nameYear
                );
            }
        }

        return $dateValue;
    }

    /**
     * Get date condition parameter (start or end)
     *
     * @param boolean $start Start date flag, otherwise - end date  OPTIONAL
     *
     * @return mixed
     */
    public function getDateCondition($start = true)
    {
        $dates = $this->getCondition(\XLite\Model\Repo\Order::P_DATE);
        $n = (true === $start) ? 0 : 1;

        return isset($dates[$n]) ? $dates[$n] : null;
    }

    /**
     * Common prefix for editable elements in lists
     *
     * NOTE: this method is requered for the GetWidget and AAdmin classes
     * TODO: after the multiple inheritance should be moved to the AAdmin class
     *
     * @return string
     */
    public function getPrefixPostedData()
    {
        return 'data';
    }

    // }}}

    // {{{ Actions

    /**
     * Search by customer
     *
     * @return void
     */
    protected function doActionSearchByCustomer()
    {
        \XLite\Core\Session::getInstance()->{$this->getSessionCellName()} = array(
            'substring' => \XLite\Core\Request::getInstance()->substring,
            'profileId' => intval(\XLite\Core\Request::getInstance()->profileId),
        );

        $this->setReturnURL($this->getURL(array('searched' => 1)));
    }

    /**
     * doActionUpdate
     *
     * @return void
     */
    protected function doActionUpdateItemsList()
    {
        $changes = $this->getOrdersChanges();

        parent::doActionUpdateItemsList();

        $updateRecent = array();
        foreach ($changes as $orderId => $change) {
            if (!empty($change['paymentStatus']) || !empty($change['shippingStatus'])) {
                $updateRecent[$orderId] = array('recent' => 0);
            }
            \XLite\Core\OrderHistory::getInstance()->registerOrderChanges($orderId, $change);
        }

        if (!empty($updateRecent)) {
            \XLite\Core\Database::getRepo('XLite\Model\Order')->updateInBatchById($updateRecent);
        }
    }

    /**
     * Do action delete
     *
     * @return void
     */
    protected function doActionDelete()
    {
        $select = \XLite\Core\Request::getInstance()->select;

        if ($select && is_array($select)) {
            \XLite\Core\Database::getRepo('XLite\Model\Order')->deleteInBatchById($select);
            \XLite\Core\TopMessage::addInfo(
                'Orders has been deleted successfully'
            );

        } else {
            \XLite\Core\TopMessage::addWarning('Please select the orders first');
        }
    }

    /**
     * Save search conditions
     *
     * @return void
     */
    protected function doActionSearch()
    {
        // Clear stored search conditions
        \XLite\Core\Session::getInstance()->{$this->getSessionCellName()} = array();

        $this->prepareSearchParams();

        $this->setReturnURL($this->getURL(array('searched' => 1)));
    }

    /**
     * Clear search conditions for searchTotal
     *
     * @return void
     */
    protected function clearSearchTotalConditions()
    {
        $searchTotalSessionCell = \XLite\View\ItemsList\Model\Order\Admin\SearchTotal::getSessionCellName();
        \XLite\Core\Session::getInstance()->{$searchTotalSessionCell} = array();
    }

    /**
     * Clear search conditions
     *
     * @return void
     */
    protected function doActionClearSearch()
    {
        \XLite\Core\Session::getInstance()->{$this->getSessionCellName()} = array();
        $this->clearSearchTotalConditions();

        $this->setReturnURL($this->getURL(array('searched' => 1)));
    }

    /**
     * Process 'no action'
     *
     * @return void
     */
    protected function doNoAction()
    {
        parent::doNoAction();

        if (\XLite\Core\Request::getInstance()->fast_search) {

            // Clear stored search conditions
            \XLite\Core\Session::getInstance()->{$this->getSessionCellName()} = array();
            $this->clearSearchTotalConditions();
            $this->prepareSearchParams();

            // Get ItemsList widget
            $widget = new \XLite\View\ItemsList\Model\Order\Admin\Search();

            // Search for single order
            $entity = $widget->searchForSingleEntity();

            if ($entity && $entity instanceOf \XLite\Model\Order) {
                // Prepare redirect to order page
                $url = $this->buildURL('order', '', array('order_number' => $entity->getOrderNumber()));
                $this->setReturnURL($url);
            }
        }
    }

    /**
     * Get search filter
     *
     * @return \XLite\Model\SearchFilter
     */
    public function getSearchFilter()
    {
        $filter = parent::getSearchFilter();

        if (!$filter && 'recent' == \XLite\Core\Request::getInstance()->filter_id) {

            $searchParams = array(
                \XLite\Model\Repo\Order::P_RECENT => 1,
                static::PARAM_SEARCH_FILTER_ID    => 'recent',
            );

            $filter = new \XLite\Model\SearchFilter();
            $filter->setParameters($searchParams);
        }

        return $filter;
    }

    /**
     * Get currently used filter
     *
     * @return \XLite\Model\SearchFilter
     */
    public function getCurrentSearchFilter()
    {
        $filter = parent::getCurrentSearchFilter();

        if (!$filter) {
            $cellName = $this->getSessionCellName();
            $searchParams = \XLite\Core\Session::getInstance()->$cellName;
            if (isset($searchParams[static::PARAM_SEARCH_FILTER_ID])
                && 'recent' === $searchParams[static::PARAM_SEARCH_FILTER_ID]
            ) {
                $filter = new \XLite\Model\SearchFilter();
                $filter->setId('recent');
                $filter->setName(static::t('Orders awaiting processing'));
            }
        }

        return $filter;
    }

    /**
     * Initialize search parameters from request data
     *
     * @return void
     */
    protected function prepareSearchParams()
    {
        $ordersSearch = $this->getSearchFilterParams();

        if (!$ordersSearch) {
            // Prepare dates
            $this->startDate = $this->getDateValue('startDate');
            $this->endDate   = $this->getDateValue('endDate', true);

            if (
                0 === $this->startDate
                || 0 === $this->endDate
                || $this->startDate > $this->endDate
            ) {
                $date = getdate(\XLite\Core\Converter::time());

                $this->startDate = mktime(0, 0, 0, $date['mon'], 1, $date['year']);
                $this->endDate   = mktime(0, 0, 0, $date['mon'], $date['mday'], $date['year']);
            }

            foreach ($this->getSearchParams() as $modelParam => $requestParam) {
                if (\XLite\Model\Repo\Order::P_DATE === $requestParam) {
                    $ordersSearch[$requestParam] = array($this->startDate, $this->endDate);

                } elseif (isset(\XLite\Core\Request::getInstance()->$requestParam)) {
                    $ordersSearch[$requestParam] = \XLite\Core\Request::getInstance()->$requestParam;
                }
            }

            if (!isset($ordersSearch[\XLite\Model\Repo\Order::P_PROFILE_ID])) {
                $ordersSearch[\XLite\Model\Repo\Order::P_PROFILE_ID] = 0;
            }
        }

        \XLite\Core\Session::getInstance()->{$this->getSessionCellName()} = $ordersSearch;
    }

    /**
     * Get order changes from request
     *
     * @return array
     */
    protected function getOrdersChanges()
    {
        $changes = array();

        foreach ($this->getPostedData() as $orderId => $data) {
            $order = \XLite\Core\Database::getRepo('XLite\Model\Order')->find($orderId);

            foreach ($data as $name => $value) {
                if ('status' === $name) {
                    continue;
                }

                $dataFromOrder = $order->{'get' . ucfirst($name)}();

                if (
                    $dataFromOrder
                    && $dataFromOrder->getId() !== intval($value)
                ) {
                    $changes[$orderId][$name] = array(
                        'old' => $dataFromOrder,
                        'new' => $value,
                    );
                }
            }
        }

        return $changes;
    }

    // }}}
}
