<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\VendorMessages\View\Order;

/**
 * Orders search result item widget
 */
class ListItem extends \XLite\View\Order\ListItem implements \XLite\Base\IDecorator
{
    /**
     * Count unread messages
     *
     * @param \XLite\Model\Order $order Order
     *
     * @return integer
     */
    protected function countUnreadMessages(\XLite\Model\Order $order)
    {
        return $order->countUnreadMessages();
    }
}
