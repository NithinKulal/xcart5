<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Pager\Admin\Model;

/**
 * Common pager for model-based items lists
 */
abstract class AModel extends \XLite\View\Pager\Admin\AAdmin
{
    /**
     * Check visibility
     * 
     * @return boolean
     */
    public function isVisible()
    {
        return parent::isVisible();
    }

    /**
     * Get items per page (default)
     *
     * @return integer
     */
    protected function getItemsPerPageDefault()
    {
        return 20;
    }
}

