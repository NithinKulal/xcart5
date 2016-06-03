<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Pager\Admin\Model\Shipping;

/**
 * Table-based pager
 */
class Markup extends \XLite\View\Pager\Admin\Model\Table
{
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
     * Get items per page ranges list
     *
     * @return array
     */
    protected function getItemsPerPageRanges()
    {
        return array(10, 25, 50);
    }
}
