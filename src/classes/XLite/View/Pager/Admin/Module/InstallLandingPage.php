<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Pager\Admin\Module;

/**
 * Pager for the marketplace landing page
 */
class InstallLandingPage extends \XLite\View\Pager\Admin\Module\Install
{
    /**
     * getItemsPerPageDefault
     *
     * @return integer
     */
    protected function getItemsPerPageDefault()
    {
        return 10000000;
    }

    /**
     * Return true as 'Items-per-page' selector should be visible.
     * It used to add common tools for 'Landing page' modules list
     *
     * @return boolean
     */
    protected function isItemsPerPageVisible()
    {
        return true;
    }
}
