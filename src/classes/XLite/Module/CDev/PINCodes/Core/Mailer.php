<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\PINCodes\Core;

/**
 * Mailer
 *
 */
abstract class Mailer extends \XLite\Core\Mailer implements \XLite\Base\IDecorator
{
    const TYPE_ACQUIRE_PIN_CODES_FAILED_LINKS = 'siteAdmin';

    /**
     * Send failed acquiring pin codes message
     *
     * @param \XLite\Model\Order $order Order model
     *
     * @return string
     */
    public static function sendAcquirePinCodesFailedAdmin(\XLite\Model\Order $order)
    {
        static::register('order', $order);

        static::compose(
            static::TYPE_ACQUIRE_PIN_CODES_FAILED_LINKS,
            static::getOrdersDepartmentMail(),
            static::getOrdersDepartmentMail(),
            'modules/CDev/PINCodes/acquire_pin_codes_failed',
            array(),
            true,
            \XLite::ADMIN_INTERFACE,
            static::getMailer()->getLanguageCode(\XLite::ADMIN_INTERFACE)
        );

        return static::getMailer()->getLastError();
    }
}
