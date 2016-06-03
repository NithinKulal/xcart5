<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Customer;

/**
 * Orders list
 */
class OrderList extends \XLite\Controller\Customer\ACustomer
{
    /**
     * Controller parameters
     *
     * @var array
     */
    protected $params = array('target', 'page');

    /**
     * Handles the request
     *
     * @return void
     */
    public function handleRequest()
    {
        parent::handleRequest();

        if (isset(\XLite\Core\Request::getInstance()->pageId)) {
            $ordersSearch = \XLite\Core\Session::getInstance()->orders_search;

            if (!is_array($ordersSearch)) {
                $ordersSearch = \XLite\Model\Order::getDefaultSearchConditions();
            }

            $ordersSearch['pageId'] = (int) \XLite\Core\Request::getInstance()->pageId;

            \XLite\Core\Session::getInstance()->orders_search = $ordersSearch;
        }
    }

    /**
     * Check if current page is accessible
     *
     * @return boolean
     */
    public function checkAccess()
    {
        return parent::checkAccess() && \XLite\Core\Auth::getInstance()->isLogged() && $this->checkProfile();
    }

    /**
     * Setter
     *
     * @param string $name  Property name
     * @param mixed  $value Property value
     *
     * @return void
     */
    public function set($name, $value)
    {
        switch ($name) {
            case 'startDate':
            case 'endDate':
                $value = (int) $value;
                break;

            default:
        }

        parent::set($name, $value);
    }

    /**
     * Check - controller must work in secure zone or not
     *
     * @return boolean
     */
    public function isSecure()
    {
        return \XLite\Core\Config::getInstance()->Security->customer_security;
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

    /**
     * Check whether the title is to be displayed in the content area
     *
     * @return boolean
     */
    public function isTitleVisible()
    {
        return \XLite\Core\Request::getInstance()->widget;
    }

    /**
     * Common method to determine current location
     *
     * @return string
     */
    protected function getLocation()
    {
        return $this->getTitle();
    }

    /**
     * Add part to the location nodes list
     *
     * @return void
     */
    protected function addBaseLocation()
    {
        parent::addBaseLocation();

        $this->addLocationNode(static::t('My account'));
    }

    /**
     * Save search conditions
     * TODO: to revise
     *
     * @return void
     */
    protected function doActionSearch()
    {
        $ordersSearch = \XLite\Core\Session::getInstance()->orders_search;

        if (!is_array($ordersSearch)) {
            $ordersSearch = \XLite\Model\Order::getDefaultSearchConditions();
        }

        if (isset(\XLite\Core\Request::getInstance()->orderNumber)) {
            $ordersSearch['orderNumber'] = \XLite\Core\Request::getInstance()->orderNumber;
        }

        if (isset(\XLite\Core\Request::getInstance()->status)) {
            $ordersSearch['status'] = \XLite\Core\Request::getInstance()->status;
        }

        if (
            isset(\XLite\Core\Request::getInstance()->startDateMonth)
            && isset(\XLite\Core\Request::getInstance()->startDateDay)
            && isset(\XLite\Core\Request::getInstance()->startDateYear)
        ) {
            $ordersSearch['startDate'] = mktime(
                0, 0, 0,
                intval(\XLite\Core\Request::getInstance()->startDateMonth),
                intval(\XLite\Core\Request::getInstance()->startDateDay),
                intval(\XLite\Core\Request::getInstance()->startDateYear)
            );

        } elseif (isset(\XLite\Core\Request::getInstance()->startDate)) {
            $time = strtotime(\XLite\Core\Request::getInstance()->startDate);

            if (
                false !== $time
                && -1 !== $time
            ) {
                $ordersSearch['startDate'] = mktime(
                    0, 0, 0,
                    date('m', $time),
                    date('d', $time),
                    date('Y', $time)
                );

            } elseif (0 == strlen(\XLite\Core\Request::getInstance()->startDate)) {
                $ordersSearch['startDate'] = '';
            }
        }

        if (
            isset(\XLite\Core\Request::getInstance()->endDateMonth)
            && isset(\XLite\Core\Request::getInstance()->endDateDay)
            && isset(\XLite\Core\Request::getInstance()->endDateYear)
        ) {
            $ordersSearch['endDate'] = mktime(
                23, 59, 59,
                intval(\XLite\Core\Request::getInstance()->endDateMonth),
                intval(\XLite\Core\Request::getInstance()->endDateDay),
                intval(\XLite\Core\Request::getInstance()->endDateYear)
            );

        } elseif (isset(\XLite\Core\Request::getInstance()->endDate)) {
            $time = strtotime(\XLite\Core\Request::getInstance()->endDate);

            if (
                false !== $time
                && -1 !== $time
            ) {
                $ordersSearch['endDate'] = mktime(
                    23, 59, 59,
                    date('m', $time),
                    date('d', $time),
                    date('Y', $time)
                );

            } elseif (0 == strlen(\XLite\Core\Request::getInstance()->endDate)) {
                $ordersSearch['endDate'] = '';
            }
        }

        if (\XLite\Core\Request::getInstance()->sortCriterion) {
            $ordersSearch['sortCriterion'] = \XLite\Core\Request::getInstance()->sortCriterion;
        }

        if (\XLite\Core\Request::getInstance()->sortOrder) {
            $ordersSearch['sortOrder'] = \XLite\Core\Request::getInstance()->sortOrder;
        }

        if (isset(\XLite\Core\Request::getInstance()->pageId)) {
            $ordersSearch['pageId'] = intval(\XLite\Core\Request::getInstance()->pageId);
        }

        \XLite\Core\Session::getInstance()->orders_search = $ordersSearch;

        $this->setReturnURL($this->buildURL('order_list'));
    }

    /**
     * Reset search conditions
     *
     * @return void
     */
    protected function doActionReset()
    {
        \XLite\Core\Session::getInstance()->orders_search = \XLite\Model\Order::getDefaultSearchConditions();

        $this->setReturnURL($this->buildURL('order_list'));
    }
}
