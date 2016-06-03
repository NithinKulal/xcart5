<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Sale\View\Pager;

/**
 * Pager for Sale products list
 */
class Pager extends \XLite\View\Pager\APager
{
    /**
     * Return number of items per page
     *
     * @return integer
     */
    protected function getItemsPerPageDefault()
    {
        return 0;
    }

    /**
     * Return number of pages to display
     *
     * @return integer
     */
    protected function getPagesPerFrame()
    {
        return 0;
    }

    /**
     * Hide "pages" part of widget
     *
     * @return boolean
     */
    protected function isPagesListVisible()
    {
        return false;
    }

    /**
     * Hide "items per page" part of widget
     *
     * @return boolean
     */
    protected function isItemsPerPageVisible()
    {
        return false;

    }
}
