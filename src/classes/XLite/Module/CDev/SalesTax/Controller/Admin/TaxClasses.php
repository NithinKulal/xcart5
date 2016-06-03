<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\SalesTax\Controller\Admin;

/**
 * Tax classes controller
 */
class TaxClasses extends \XLite\Controller\Admin\TaxClasses implements \XLite\Base\IDecorator
{
    /**
     * Check - is current place enabled or not
     *
     * @return boolean
     */
    public static function isEnabled()
    {
        return true;
    }
}
