<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Customer;

/**
 * Products search
 */
class Search extends \XLite\Controller\Customer\ACustomer
{
    /**
     * Get search condition parameter by name TODO refactor with XLite\Controller\Admin\ProductList::getCondition()
     *
     * @param string $paramName Name of parameter
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
     * Return 'checked' attribute for parameter.
     *
     * @param string $paramName Name of parameter
     * @param mixed  $value     Value to check with OPTIONAL
     *
     * @return string
     */
    public function getChecked($paramName, $value = 'Y')
    {
        return $value === $this->getCondition($paramName) ? 'checked' : '';
    }

    /**
     * Get page title
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Search');
    }

    /**
     * Common method to determine current location
     *
     * @return string
     */
    protected function getLocation()
    {
        return static::t('Search results');
    }

    /**
     * doActionSearch TODO refactor with XLite\Controller\Admin\ProductList::doActionSearch()
     *
     * @return void
     */
    protected function doActionSearch()
    {
        $sessionCell    = \XLite\View\ItemsList\Product\Customer\Search::getSearchSessionCellName();
        $searchParams   = \XLite\View\ItemsList\Product\Customer\Search::getSearchParams();
        $advancedParams = array_diff(\XLite\View\ItemsList\Product\Customer\Search::getSearchParams(), \XLite\View\ItemsList\Product\Customer\Search::getBasicSearchParams());

        $productsSearch = array();

        $cBoxFields     = array(
            \XLite\View\ItemsList\Product\Customer\Search::PARAM_SEARCH_IN_SUBCATS
        );

        foreach ($searchParams as $modelParam => $requestParam) {
            if (isset(\XLite\Core\Request::getInstance()->$requestParam)) {
                $productsSearch[$requestParam] = \XLite\Core\Request::getInstance()->$requestParam;
            }
        }

        foreach ($cBoxFields as $requestParam) {
            $productsSearch[$requestParam] = isset(\XLite\Core\Request::getInstance()->$requestParam)
            ? 1
            : 0;
        }

        \XLite\Core\Session::getInstance()->{$this->getAdvancedPanelCellName()} = array_intersect(array_keys($productsSearch), array_values($advancedParams));
        \XLite\Core\Session::getInstance()->{$sessionCell} = $productsSearch;

        $this->setReturnURL($this->buildURL('search', '', array('mode' => 'search')));
    }

    /**
     * Checks session var and returns true, if advanced panel should be shown
     *
     * @return boolean
     */
    public function showAdvancedPanel()
    {
        return \XLite\Core\Session::getInstance()->{$this->getAdvancedPanelCellName()};
    }

    /**
     * Return session var name related to advanced search panel
     *
     * @return string
     */
    public function getAdvancedPanelCellName()
    {
        return 'show_advanced_search_panel';
    }

    /**
     * Get search conditions TODO refactor with XLite\Controller\Admin\ProductList::getConditions()
     *
     * @return array
     */
    protected function getConditions()
    {
        $searchParams = \XLite\Core\Session::getInstance()
            ->{\XLite\View\ItemsList\Product\Customer\Search::getSearchSessionCellName()};

        if (!is_array($searchParams)) {
            $searchParams = array();
        }

        return $searchParams;
    }
}
