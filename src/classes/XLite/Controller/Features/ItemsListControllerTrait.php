<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Features;

/**
 * ItemsListControllerTrait
 */
trait ItemsListControllerTrait
{

    /**
     * Runtime cache
     *
     * @var \XLite\View\ItemsList\Model\AModel
     */
    protected $itemsListRuntime;

    /**
     * Get itemsList class
     *
     * @return string
     */
    public function getItemsListClass()
    {
        return \XLite\Core\Request::getInstance()->itemsList;
    }

    /**
     * Get search values storage
     *
     * @return \XLite\View\ItemsList\ISearchValuesStorage
     */
    protected function getSearchValuesStorage()
    {
        $class = $this->getItemsListClass();

        return $class::getSearchValuesStorage(true);
    }

    /**
     * Get items list widget
     *
     * @return \XLite\View\ItemsList\Model\AModel
     */
    protected function getItemsList()
    {
        if (!$this->itemsListRuntime) {
            $class = $this->getItemsListClass();
            // Check conditions
            // TODO: Move them somethere else
            if (!$class || !class_exists($class)) {
                $message = sprintf('Items list with class %s not exists', $class);
                throw new \Exception($message, 1);
            }

            if (!method_exists($class, 'processQuick')) {
                $message = sprintf('Items list %s does\'n have processQuick() method', $class);
                throw new \Exception($message, 1);
            }

            $this->itemsListRuntime = new $class;
        }

        return $this->itemsListRuntime;
    }

    /**
     * Update list
     *
     * @return void
     */
    protected function doActionUpdateItemsList()
    {
        $list = $this->getItemsList();

        $list->processQuick();
    }

    // {{{ Search

    /**
     * Save search conditions
     *
     * @return void
     */
    protected function fillSearchValuesStorage()
    {
        $storage = $this->getSearchValuesStorage();

        // Fill search conditions from requst
        $className = $this->getItemsListClass();
        $searchConditionsRequestNames = $className::getSearchParams();

        foreach ($searchConditionsRequestNames as $name => $condition) {
            $requestName = is_string($condition)
                ? $condition
                : $name;
            $storage->setValue($requestName, \XLite\Core\Request::getInstance()->$requestName);
        }

        $storage->update();
    }

    /**
     * Save search conditions
     *
     * @return void
     */
    protected function doActionSearchItemsList()
    {
        $this->fillSearchValuesStorage();
    }


    /**
     * Save search conditions
     *
     * @return void
     */
    protected function doActionSearchItemsListViaOldMode()
    {
        $cellName = $this->getSessionCellName();

        \XLite\Core\Session::getInstance()->$cellName = $this->getSearchParams();
    }

    // }}}

    /**
     * Do action delete
     *
     * @return void
     */
    protected function doActionDelete()
    {
        $select = \XLite\Core\Request::getInstance()->select;

        if ($select && is_array($select)) {
            $removePrefix = $this->getItemsList()->getRemoveDataPrefix();
            \XLite\Core\Request::getInstance()->{$removePrefix} = $select;
            \XLite\Core\Request::getInstance()->mapRequest();

            $this->doActionUpdateItemsList();
        } else {
            \XLite\Core\TopMessage::addWarning('Please select the entities first');
        }
    }

    // {{{ DEPRECATED part below, left for backwards compatibility

    /**
     * Return search parameters
     *
     * @return array
     */
    protected function getSearchParams()
    {
        $className = $this->getItemsListClass();
        return $className::getSearchParams();
    }

    /**
     * Return params list to use for search
     *
     * @return array
     */
    protected function getSearchCondition()
    {
        return $this->mapSearchConditionsFromRequest();
    }

    /**
     * Return search parameters
     *
     * @return array
     */
    protected function mapSearchConditionsFromRequest()
    {
        $sessionSearchConditions = $this->getSessionSearchConditions();

        // Fill search conditions from requst
        $className = $this->getItemsListClass();
        $searchConditionsRequestNames = $className::getSearchParams();
        foreach ($searchConditionsRequestNames as $name => $condition) {
            if (isset(\XLite\Core\Request::getInstance()->$name)) {
                $sessionSearchConditions[$name] = \XLite\Core\Request::getInstance()->$name;
            }
        }

        return $sessionSearchConditions;
    }

    /**
     * Get search conditions from session
     *
     * @return array
     */
    protected function getSessionSearchConditions()
    {
        $cellName = $this->getSessionCellName();

        $searchParams = \XLite\Core\Session::getInstance()->$cellName;

        if (!is_array($searchParams)) {
            $searchParams = array();
        }

        return $searchParams;
    }

    /**
     * Define the session cell name for the order list
     *
     * @return string
     */
    protected function getSessionCellName()
    {
        $className = $this->getItemsListClass();

        return $className
            ? $className::getSearchSessionCellName()
            : null;
    }

    /**
     * Get search condition parameter by name
     * N.B. Left for backwards compatibility. Do not use
     *
     * @param string $paramName Parameter name
     *
     * @return mixed
     */
    public function getCondition($paramName)
    {
        $searchParams = $this->mapSearchConditionsFromRequest();

        return isset($searchParams[$paramName])
            ? $searchParams[$paramName]
            : null;
    }

    // }}}
}
