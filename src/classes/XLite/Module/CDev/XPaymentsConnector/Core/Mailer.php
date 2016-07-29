<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\XPaymentsConnector\Core;

/**
 * Mailer 
 */
class Mailer extends \XLite\Core\Mailer implements \XLite\Base\IDecorator
{
    /**
     * Check if the email is sent to Admin curently
     *
     * @return bool 
     */
    public static function isMailSendToAdmin()
    {
        return \XLite::ADMIN_INTERFACE == \XLite\Core\Layout::getInstance()->getMailInterface();
    }
}
