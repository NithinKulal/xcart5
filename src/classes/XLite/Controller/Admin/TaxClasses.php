<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

/**
 * Tax classes controller
 */
class TaxClasses extends \XLite\Controller\Admin\ACL\Catalog
{
    /**
     * Check - is current place enabled or not
     *
     * @return boolean
     */
    static public function isEnabled()
    {
        return false;
    }

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Taxes');
    }
}
