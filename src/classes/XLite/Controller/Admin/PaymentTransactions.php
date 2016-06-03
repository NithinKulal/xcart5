<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

/**
 * Payment transactions controller
 */
class PaymentTransactions extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Check ACL permissions
     *
     * @return boolean
     */
    public function checkACL()
    {
        return parent::checkACL() || \XLite\Core\Auth::getInstance()->isPermissionAllowed('manage orders');
    }

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Payment transactions');
    }
}
