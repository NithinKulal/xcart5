<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\GoogleAnalytics\Model\Order\Status;

/**
 * Order payment status
 */
abstract class Payment extends \XLite\Model\Order\Status\Payment implements \XLite\Base\IDecorator
{
    /**
     * Get open order statuses
     *
     * @return array
     */
    public static function getGANotPaidStatuses()
    {
        return array(
            static::STATUS_QUEUED,
            static::STATUS_REFUNDED,
            static::STATUS_DECLINED,
            static::STATUS_CANCELED,
        );
    }

    /**
     * Return status handlers list
     *
     * @return array
     */
    public static function getStatusHandlers()
    {
        $handlers = parent::getStatusHandlers();

        foreach (static::getGANotPaidStatuses() as $status) {
            if (!isset($handlers[$status])) {
                $handlers[$status] = array(
                    static::STATUS_PAID => array()
                );
            }
            if (!isset($handlers[$status][static::STATUS_PAID])) {
                $handlers[$status][static::STATUS_PAID] = array();
            }

            // From NOTPAID to PAID state change
            array_push(
                $handlers[$status][static::STATUS_PAID],
                'registerGAPurchase'
            );

            if (!isset($handlers[static::STATUS_PAID][$status])) {
                $handlers[static::STATUS_PAID][$status] = array();
            }
            // From PAID to NOTPAID state change
            array_push(
                $handlers[static::STATUS_PAID][$status],
                'registerGARefund'
            );

            if ($status !== static::STATUS_QUEUED) {
                if (!isset($handlers[static::STATUS_QUEUED][$status])) {
                    $handlers[static::STATUS_QUEUED][$status] = array();
                }
                // From STATUS_QUEUED to NOTPAID state change
                array_push(
                    $handlers[static::STATUS_QUEUED][$status],
                    'registerGARefundFromQueued'
                );
            }
        }

        return $handlers;
    }
}