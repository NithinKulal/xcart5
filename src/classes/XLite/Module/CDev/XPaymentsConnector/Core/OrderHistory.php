<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\XPaymentsConnector\Core;

/**
 * XPayments client
 *
 */
class OrderHistory extends \XLite\Core\OrderHistory implements \XLite\Base\IDecorator
{
    /**
     * Texts for the order history event descriptions
     */
    const TXT_PLACE_ORDER_UPDATED           = 'Order updated';

    /**
     * Text for place order description
     *
     * @param integer $orderId Order id
     *
     * @return string
     */
    protected function getPlaceOrderDescription($orderId)
    {
        $alreadyPlaced = false;

        foreach (\XLite\Core\Database::getRepo('XLite\Model\OrderHistoryEvents')->find($orderId) as $event) {
            if (static::CODE_PLACE_ORDER == $event->getCode()) {
                $alreadyPlaced = true;
                break;
            }
        }

        return $alreadyPlaced
            ? static::TXT_PLACE_ORDER_UPDATED
            : static::TXT_PLACE_ORDER;
    }

}
