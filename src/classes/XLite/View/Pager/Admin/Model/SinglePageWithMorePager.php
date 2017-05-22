<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Pager\Admin\Model;

/**
 * Messages pager
 */
class SinglePageWithMorePager extends \XLite\View\Pager\Admin\Model\Table
{
    /**
     * Check if pages list is visible or not
     *
     * @return boolean
     */
    protected function isPagesListVisible()
    {
        return false;
    }

    /**
     * @return bool
     */
    protected function isItemsPerPageVisible()
    {
        return false;
    }

    /**
     * @return int
     */
    public function getItemsPerPage()
    {
        return $this->getItemsPerPageMax();
    }

    /**
     * isVisible
     *
     * @return boolean
     */
    public function isVisible()
    {
        $itemsCount = (int)$this->getParam(static::PARAM_ITEMS_COUNT);
        $maxItemsCount = (int)$this->getMaxItemsCount();
        $isMaxExceeded = $itemsCount > $maxItemsCount;

        return $this->getPages() && $isMaxExceeded;
    }

    /**
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'pager/model/table/only_more.css';

        return $list;
    }
}
