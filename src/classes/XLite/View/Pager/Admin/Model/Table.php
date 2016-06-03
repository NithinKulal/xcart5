<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Pager\Admin\Model;

/**
 * Table-based pager
 */
class Table extends \XLite\View\Pager\Admin\Model\AModel
{
    /**
     * isVisible: hide pager if table contains no data
     * 
     * @return boolean
     */
    public function isVisible()
    {
        return parent::isVisible() && 0 < $this->getItemsTotal();
    }

    /**
     * Return CSS classes for parent block of pager (list-pager by default)
     *
     * @return string
     */
    public function getCSSClasses()
    {
        return 'table-pager';
    }

    /**
     * Get items per page (default)
     *
     * @return integer
     */
    protected function getItemsPerPageDefault()
    {
        return 25;
    }

    /**
     * getDir
     *
     * @return string
     */
    protected function getDir()
    {
        return 'pager/model/table';
    }

    // {{{ Content helpers

    /**
     * Check - current page is first or not
     * 
     * @return boolean
     */
    protected function isFirstPage()
    {
        return $this->getPageId() == $this->getFirstPageId();
    }

    /**
     * Check - current page is last or not
     *
     * @return boolean
     */
    protected function isLastPage()
    {
        return $this->getPageId() == $this->getLastPageId();
    }

    /**
     * Get previous arrow class 
     * 
     * @return string
     */
    protected function getPrevClass()
    {
        return 'prev ' . ($this->isFirstPage() ? 'disabled' : 'enabled');
    }

    /**
     * Get next arrow class
     *
     * @return string
     */
    protected function getNextClass()
    {
        return 'next ' . ($this->isLastPage() ? 'disabled' : 'enabled');
    }

    /**
     * Get items per page ranges list
     * 
     * @return array
     */
    protected function getItemsPerPageRanges()
    {
        return array(10, 25, 50, 75, 100);
    }

    /**
     * Chec - items per page box visible or not
     *
     * @return boolean
     */
    protected function isItemsPerPageVisible()
    {
        $min = min($this->getItemsPerPageRanges());

        return $min < $this->getItemsTotal();
    }

    /**
     * Check - range is selected or not
     * 
     * @param integer $range Range
     *  
     * @return boolean
     */
    protected function isRangeSelected($range)
    {
        return $range == $this->getItemsPerPage();
    }

    /**
     * Preprocess page id 
     * 
     * @param integer $id Page id
     *  
     * @return integer
     */
    protected function preprocessPageId($id)
    {
        return $id + 1;
    }

    // }}}
}
