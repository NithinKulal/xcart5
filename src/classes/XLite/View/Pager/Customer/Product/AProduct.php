<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Pager\Customer\Product;

/**
 * Abstract pager class for the ProductsList widget
 */
abstract class AProduct extends \XLite\View\Pager\Customer\ACustomer
{
    /**
     * Return number of items per page
     *
     * @return integer
     */
    protected function getItemsPerPageDefault()
    {
        return (int) \XLite\Core\Config::getInstance()->General->products_per_page;
    }

    /**
     * Return maximum possible items number per page
     *
     * @return integer
     */
    protected function getItemsPerPageMax()
    {
        return (int) \XLite\Core\Config::getInstance()->General->products_per_page_max ?: parent::getItemsPerPageMax();
    }
}
