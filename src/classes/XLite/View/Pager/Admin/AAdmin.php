<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Pager\Admin;

/**
 * AAdmin
 */
abstract class AAdmin extends \XLite\View\Pager\APager
{
    /**
     * getItemsPerPageDefault
     *
     * @return integer
     */
    protected function getItemsPerPageDefault()
    {
        return 30;
    }

    /**
     * Return number of pages to display
     *
     * @return integer
     */
    protected function getPagesPerFrame()
    {
        return 7;
    }
}
