<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\VendorMessages\Core;

/**
 * Mailer
 *
 * @Decorator\Depend ({"XC\MultiVendor", "XC\VendorMessages"})
 */
abstract class MailerMultivendor extends \XLite\Core\Mailer implements \XLite\Base\IDecorator
{
    /**
     * Return Order messages link
     *
     * @param string $name Variable name
     *
     * @return string
     */
    protected function getVariableValueOrderMessagesLink($name)
    {
        $message = $this->getMessage();
        $order = $this->getOrder();

        if ($message && $order && \XLite\Module\XC\VendorMessages\Main::isWarehouse()) {
            $parentOrder = $order->getParent() ?: $order;
            if (static::getMailer()->get('targetType') == $message::AUTHOR_TYPE_CUSTOMER) {
                if ($order->getProfile()->getAnonymous()) {
                    $acc = \XLite\Core\Database::getRepo('XLite\Model\AccessControlCell')->generateAccessControlCell(
                        [$order],
                        [\XLite\Model\AccessControlZoneType::ZONE_TYPE_ORDER],
                        'resendAccessLink'
                    );

                    $url = \XLite\Core\Converter::buildPersistentAccessURL(
                        $acc,
                        'order_messages',
                        '',
                        [
                            'order_number' => $parentOrder->getOrderNumber(),
                            'recipient_id' => $order->getOrderId(),
                        ],
                        \XLite::getCustomerScript()
                    );
                } else {
                    $url = \XLite\Core\Converter::buildURL(
                        'order_messages',
                        null,
                        [
                            'order_number' => $parentOrder->getOrderNumber(),
                            'recipient_id' => $order->getOrderId(),
                        ],
                        \XLite::getCustomerScript()
                    );
                }
            } else {
                $url = \XLite\Core\Converter::buildURL(
                    'order',
                    null,
                    [
                        'page'         => 'messages',
                        'order_number' => $parentOrder->getOrderNumber(),
                        'recipient_id' => $order->getOrderId(),
                    ],
                    \XLite::getAdminScript()
                );
            }

            return '<a href="' . htmlentities(\XLite::getInstance()->getShopURL($url)) . '">'
            . $parentOrder->getOrderNumber()
            . '</a>';
        }

        return parent::getVariableValueOrderMessagesLink($name);
    }
}