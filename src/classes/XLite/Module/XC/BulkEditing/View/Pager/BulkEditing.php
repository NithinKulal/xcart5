<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\BulkEditing\View\Pager;

class BulkEditing extends \XLite\View\Pager\Admin\AAdmin
{
    /**
     * Return CSS classes to use in parent widget of pager
     *
     * @return string
     */
    public function getCSSClasses()
    {
        return parent::getCSSClasses() . ' bulk-editing-pager';
    }

    /**
     * Define the pager title
     *
     * @return string
     */
    protected function getPagerTitle()
    {
        return $this->getItemsTotal() . ' ' . static::t('items');
    }

    /**
     * getItemsPerPageDefault
     *
     * @return integer
     */
    protected function getItemsPerPageDefault()
    {
        return 10;
    }

    /**
     * Return number of pages to display
     *
     * @return integer
     */
    protected function getPagesPerFrame()
    {
        return 5;
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
     * isVisible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return true;
    }

    /**
     * getDir
     *
     * @return string
     */
    protected function getDir()
    {
        return 'modules/XC/BulkEditing/items_list/pager';
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
}
