<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\PINCodes\Model\Order\Status;

/**
 * Order payment status
 */
class Payment extends \XLite\Model\Order\Status\Payment implements \XLite\Base\IDecorator
{
    /**
     * Return status handlers list
     *
     * @return array
     */
    public static function getStatusHandlers()
    {
        $handlers = parent::getStatusHandlers();

        array_unshift(
            $handlers[static::STATUS_QUEUED][static::STATUS_DECLINED],
            'declinePIN'
        );

        array_unshift(
            $handlers[static::STATUS_QUEUED][static::STATUS_CANCELED],
            'declinePIN'
        );

        return $handlers;
    }
}
