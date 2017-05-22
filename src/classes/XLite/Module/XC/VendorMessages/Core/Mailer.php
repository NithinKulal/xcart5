<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\VendorMessages\Core;

/**
 * Mailer
 */
abstract class Mailer extends \XLite\Core\Mailer implements \XLite\Base\IDecorator
{
    /**
     * New mail type
     */
    const TYPE_VENDOR_MESSAGES = 'VendorMessages';

    /**
     * Send vendor message notification
     *
     * @param \XLite\Module\XC\VendorMessages\Model\Message $message    Message
     * @param string                                        $targetType Target type OPTIONAL
     *
     * @return string | null
     */
    public function sendVendorMessageNotification(\XLite\Module\XC\VendorMessages\Model\Message $message, $targetType = null)
    {
        $targetType = $targetType ?: $message->getTargetType();
        $recipient = $message->getInterlocutorData($targetType);

        static::register(
            array(
                'message' => $message,
                'recipientName' => $recipient['name'],
                'targetType' => $targetType,
            )
        );

        static::compose(
            static::TYPE_VENDOR_MESSAGES,
            static::getSiteAdministratorMail(),
            $recipient['email'],
            'modules/XC/VendorMessages/notification',
            array(),
            true,
            \XLite::CUSTOMER_INTERFACE,
            $recipient['language']
        );

        return static::getMailer()->getLastError();
    }

    /**
     * Returns variables names
     *
     * @return array
     */
    protected function getVariables()
    {
        return array_merge(
            parent::getVariables(),
            array(
                'order_number',
                'order_link',
                'order_messages_link',
                'message',
            )
        );
    }

    /**
     * Returns message
     *
     * @return null|\XLite\Module\XC\VendorMessages\Model\Message
     */
    protected function getMessage()
    {
        $message = static::getMailer()->get('message');

        return (is_object($message) && $message instanceof \XLite\Module\XC\VendorMessages\Model\Message)
            ? $message
            : null;
    }

    /**
     * Returns order
     *
     * @return null|\XLite\Model\Order
     */
    protected function getOrder()
    {
        return $this->getMessage() ? $this->getMessage()->getOrder() : null;
    }

    /**
     * Return Order number
     *
     * @param string $name Variable name
     *
     * @return string
     */
    protected function getVariableValueOrderNumber($name)
    {
        return $this->getOrder() ? $this->getOrder()->getOrderNumber() : null;
    }

    /**
     * Return Order link
     *
     * @param string $name Variable name
     *
     * @return string
     */
    protected function getVariableValueOrderLink($name)
    {
        $message = $this->getMessage();
        $order = $this->getOrder();

        if ($message && $order) {
            if (static::getMailer()->get('targetType') == $message::AUTHOR_TYPE_CUSTOMER) {
                $url = \XLite\Core\Converter::buildURL(
                    'order',
                    null,
                    array('order_number' => $order->getOrderNumber()),
                    \XLite::getCustomerScript()
                );
            } else {
                $url = \XLite\Core\Converter::buildURL(
                    'order',
                    null,
                    array('order_number' => $order->getOrderNumber()),
                    \XLite::getAdminScript()
                );
            }

            return '<a href="' . htmlentities(\XLite::getInstance()->getShopURL($url)) . '">'
            . $order->getOrderNumber()
            . '</a>';
        }

        return null;
    }

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

        if ($message && $order) {
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
                        ['order_number' => $order->getOrderNumber()],
                        \XLite::getCustomerScript()
                    );
                } else {
                    $url = \XLite\Core\Converter::buildURL(
                        'order_messages',
                        null,
                        ['order_number' => $order->getOrderNumber()],
                        \XLite::getCustomerScript()
                    );
                }
            } else {
                $url = \XLite\Core\Converter::buildURL(
                    'order',
                    null,
                    [
                        'page' => 'messages',
                        'order_number' => $order->getOrderNumber(),
                    ],
                    \XLite::getAdminScript()
                );
            }

            return '<a href="' . htmlentities(\XLite::getInstance()->getShopURL($url)) . '">'
            . $order->getOrderNumber()
            . '</a>';
        }

        return null;
    }

    /**
     * Return message body
     *
     * @param string $name Variable name
     *
     * @return string
     */
    protected function getVariableValueMessage($name)
    {
        return $this->getMessage() ? $this->getMessage()->getPublicBody() : null;
    }
}