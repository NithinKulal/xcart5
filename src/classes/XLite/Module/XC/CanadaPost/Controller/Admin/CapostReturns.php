<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CanadaPost\Controller\Admin;

/**
 * Canada Post returns controller
 */
class CapostReturns extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Check ACL permissions
     *
     * @return boolean
     */
    public function checkACL()
    {
        return parent::checkACL()
            || \XLite\Core\Auth::getInstance()->isPermissionAllowed('manage returns');
    }

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Search for returns');
    }

    /**
     * Return date value
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
     * Get search condition parameter by name
     *
     * @param string $paramName Parameter name
     *
     * @return mixed
     */
    public function getCondition($paramName)
    {
        $searchParams = $this->getConditions();

        return isset($searchParams[$paramName])
            ? $searchParams[$paramName]
            : null;
    }

    /**
     * Get search conditions
     *
     * @return array
     */
    protected function getConditions()
    {
        $name = \XLite\Module\XC\CanadaPost\View\ItemsList\Model\ProductsReturn::getSessionCellName();
        $searchParams = \XLite\Core\Session::getInstance()->$name;

        return is_array($searchParams) ? $searchParams : array();
    }

    // {{{ Actions

    /**
     * Update returns list
     *
     * @return void
     */
    protected function doActionUpdate()
    {
        $list = new \XLite\Module\XC\CanadaPost\View\ItemsList\Model\ProductsReturn();
        $list->processQuick();
    }

    /**
     * Save search conditions
     *
     * @return void
     */
    protected function doActionSearch()
    {
        $returnsSearch = array();
        $searchParams  = \XLite\Module\XC\CanadaPost\View\ItemsList\Model\ProductsReturn::getSearchParams();

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

        foreach ($searchParams as $modelParam => $requestParam) {
            if (isset(\XLite\Core\Request::getInstance()->$requestParam)) {
                $returnsSearch[$requestParam] = \XLite\Core\Request::getInstance()->$requestParam;
            }
        }

        $name = \XLite\Module\XC\CanadaPost\View\ItemsList\Model\ProductsReturn::getSessionCellName();
        \XLite\Core\Session::getInstance()->$name = $returnsSearch;

        $this->setReturnURL($this->getURL(array('searched' => 1)));
    }


    /**
     * Clear search conditions
     *
     * @return void
     */
    protected function doActionClearSearch()
    {
        $name = \XLite\Module\XC\CanadaPost\View\ItemsList\Model\ProductsReturn::getSessionCellName();
        \XLite\Core\Session::getInstance()->$name = array();

        $this->setReturnURL($this->getURL(array('searched' => 1)));
    }

    // }}}
}
