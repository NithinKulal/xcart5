<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\News\View\Pager;

/**
 * Abstract pager class for the NewsMessages widget
 */
class NewsMessages extends \XLite\View\Pager\Customer\ACustomer
{
    /**
     * Return number of items per page
     *
     * @return integer
     */
    protected function getItemsPerPageDefault()
    {
        return intval(\XLite\Core\Config::getInstance()->XC->News->items_per_page);
    }
}
