<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Pager\Admin\Order;

/**
 * Abstract pager class for the OrdersList widget
 */
abstract class AOrder extends \XLite\View\Pager\Admin\AAdmin
{
    /**
     * Return number of items per page
     *
     * @return integer
     */
    protected function getItemsPerPageDefault()
    {
        return intval(\XLite\Core\Config::getInstance()->General->orders_per_page);
    }
}
