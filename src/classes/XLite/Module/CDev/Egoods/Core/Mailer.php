<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Egoods\Core;

/**
 * Mailer
 */
abstract class Mailer extends \XLite\Core\Mailer implements \XLite\Base\IDecorator
{
    const TYPE_EGOODS_LINKS = 'siteAdmin';

    /**
     * Send e-goods links
     *
     * @param \XLite\Model\Order $order Order model
     *
     * @return void
     */
    public static function sendEgoodsLinks(\XLite\Model\Order $order)
    {
        static::register('order', $order);

        static::sendEgoodsLinksCustomer($order);
    }

    /**
     * Send e-goods links to customer
     *
     * @param \XLite\Model\Order $order Order model
     *
     * @return void
     */
    public static function sendEgoodsLinksCustomer(\XLite\Model\Order $order)
    {
        static::compose(
            static::TYPE_EGOODS_LINKS,
            static::getOrdersDepartmentMail(),
            $order->getProfile()->getLogin(),
            'modules/CDev/Egoods/egoods_links',
            array(),
            true,
            \XLite::CUSTOMER_INTERFACE,
            static::getMailer()->getLanguageCode(\XLite::CUSTOMER_INTERFACE, $order->getProfile()->getLanguage())
        );
    }
}
