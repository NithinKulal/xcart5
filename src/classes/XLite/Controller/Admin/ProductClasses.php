<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

/**
 * Product classes controller
 */
class ProductClasses extends \XLite\Controller\Admin\ACL\Catalog
{
    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Classes & attributes');
    }

    /**
     * Update list
     *
     * @return void
     */
    protected function doActionUpdate()
    {
        $list = new \XLite\View\ItemsList\Model\ProductClass;
        $list->processQuick();
    }

    // {{{ Search

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
     * Save search conditions
     *
     * @return void
     */
    protected function doActionSearch()
    {
        $cellName = \XLite\View\ItemsList\Model\ProductClass::getSessionCellName();

        \XLite\Core\Session::getInstance()->$cellName = $this->getSearchParams();
    }

    /**
     * Return search parameters
     *
     * @return array
     */
    protected function getSearchParams()
    {
        $searchParams = $this->getConditions();

        foreach (
            \XLite\View\ItemsList\Model\ProductClass::getSearchParams() as $requestParam
        ) {
            if (isset(\XLite\Core\Request::getInstance()->$requestParam)) {
                $searchParams[$requestParam] = \XLite\Core\Request::getInstance()->$requestParam;
            }
        }

        return $searchParams;
    }

    /**
     * Get search conditions
     *
     * @return array
     */
    protected function getConditions()
    {
        $cellName = \XLite\View\ItemsList\Model\ProductClass::getSessionCellName();

        $searchParams = \XLite\Core\Session::getInstance()->$cellName;

        if (!is_array($searchParams)) {
            $searchParams = array();
        }

        return $searchParams;
    }

    // }}}

}
