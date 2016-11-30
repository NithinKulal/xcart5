<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MailChimp\XDependencies\Controller\Admin;

use XLite\Core\Auth;

/**
 * MailChimp customer subscriptions
 *
 * @Decorator\Depend ("XC\MultiVendor")
 */
class MailchimpSubscriptions extends \XLite\Module\XC\MailChimp\Controller\Admin\MailchimpSubscriptions implements \XLite\Base\IDecorator
{
    /**
     * Check ACL permissions
     *
     * @return boolean
     */
    public function checkACL()
    {
        return parent::checkACL() || Auth::getInstance()->isVendor();
    }
}
