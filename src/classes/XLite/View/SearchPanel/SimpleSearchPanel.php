<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\SearchPanel;

/**
 * Main admin profile search panel
 */
class SimpleSearchPanel extends \XLite\View\SearchPanel\ASearchPanel
{
    const CONDITION_TYPE            = 'type';
    const CONDITION_TYPE_VISIBLE    = 'visibleConditions';
    const CONDITION_TYPE_HIDDEN     = 'hiddenConditions';

    /**
     * Get form class
     *
     * @return string
     */
    protected function getFormClass()
    {
        return '\XLite\View\Form\ItemsList\AItemsListSearch';
    }

    /**
     * Get container form class
     *
     * @return string
     */
    protected function getContainerClass()
    {
        $itemsListClass = $this->getItemsList()
            ? 'searchpanel-' . $this->getItemsList()->getIdentifierClass()
            : '';

        return parent::getContainerClass() . ' ' . $itemsListClass;
    }

    /**
     * Define the items list CSS class with which the search panel must be linked
     *
     * @return string
     */
    protected function getLinkedItemsList()
    {
        $itemsListClass = $this->getItemsList()
            ? '.widget.items-list.' . $this->getItemsList()->getIdentifierClass()
            : '';

        return parent::getLinkedItemsList() . $itemsListClass;
    }

    /**
     * Define conditions
     *
     * @return array
     */
    protected function defineConditions()
    {
        $conditions = array();
        if ($this->getItemsList() && $this->getItemsList()->getSearchParams()) {
            $widgets = array_filter(
                $this->getItemsList()->getSearchParams(),
                function($searchParam){
                    $isVisibleCondition = !isset($searchParam['widget'][static::CONDITION_TYPE])
                        || $searchParam['widget'][static::CONDITION_TYPE] === static::CONDITION_TYPE_VISIBLE;
                    return isset($searchParam['widget']) && $isVisibleCondition;
                }
            );
            $conditions = array_map(
                function($searchParam){
                    return $searchParam['widget'];
                },
                $widgets
            );
        }

        return array_merge(
            parent::defineConditions(),
            $conditions
        );
    }

    /**
     * Define hidden conditions
     *
     * @return array
     */
    protected function defineHiddenConditions()
    {
        $conditions = array();
        if ($this->getItemsList() && $this->getItemsList()->getSearchParams()) {
            $widgets = array_filter(
                $this->getItemsList()->getSearchParams(),
                function($searchParam){
                    $isHiddenCondition = isset($searchParam['widget'][static::CONDITION_TYPE])
                        && $searchParam['widget'][static::CONDITION_TYPE] === static::CONDITION_TYPE_HIDDEN;
                    return isset($searchParam['widget']) && $isHiddenCondition;
                }
            );
            $conditions = array_map(
                function($searchParam){
                    return $searchParam['widget'];
                },
                $widgets
            );
        }

        return array_merge(
            parent::defineHiddenConditions(),
            $conditions
        );
    }
}
