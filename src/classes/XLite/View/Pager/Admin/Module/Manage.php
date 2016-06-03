<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Pager\Admin\Module;

/**
 * Pager for the modules search page
 */
class Manage extends \XLite\View\Pager\Admin\Module\AModule
{
    /**
     * getItemsPerPageDefault
     *
     * @return integer
     */
    protected function getItemsPerPageDefault()
    {
        return 50;
    }

    /**
     * Return current list name
     *
     * @return string
     */
    protected function getListName()
    {
        return 'modules.pager';
    }

    /**
     * Define the pager bottom title
     *
     * @return string
     */
    protected function getPagerBottomTitle()
    {
        return '';
    }

    /**
     * Get list of keys to exclude from request parameters
     *
     * @return array
     */
    protected function getFilterRequestParams()
    {
        return array();
    }

    /**
     * Return true if 'Items-per-page' selector is visible
     *
     * @return boolean
     */
    protected function isItemsPerPageVisible()
    {
        $ranges = $this->getItemsPerPageRanges();

        $min = array_shift($ranges);

        return $min < $this->getItemsTotal();
    }
}
