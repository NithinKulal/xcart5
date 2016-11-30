<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\Core;

/**
 * Mailer
 */
class Mailer extends \XLite\Core\Mailer implements \XLite\Base\IDecorator
{
    /**
     * Send created order mail to customer
     *
     * @param string             $templatesDirectory
     * @param string             $to
     * @param string             $interface
     * @param \XLite\Model\Order $order
     *
     * @return bool
     */
    public static function sendOrderRelatedPreview($templatesDirectory, $to, $interface, \XLite\Model\Order $order)
    {
        static::register('order', $order);
        static::register('recipientName', $order->getProfile()->getName());

        if (\XLite\Core\Config::getInstance()->NotificationAttachments->attach_pdf_invoices) {
            static::attachInvoice($order, \XLite::CUSTOMER_INTERFACE);
        }

        return static::compose(
            'siteAdmin', // unused
            static::getOrdersDepartmentMail(),
            $to,
            $templatesDirectory,
            [],
            true,
            $interface,
            $interface === \XLite::CUSTOMER_INTERFACE
                ? static::getMailer()->getLanguageCode(\XLite::CUSTOMER_INTERFACE, $order->getProfile()->getLanguage())
                : static::getMailer()->getLanguageCode(\XLite::ADMIN_INTERFACE),
            true
        );
    }
}
