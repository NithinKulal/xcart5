<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\NotFinishedOrders\View\ItemsList\Model;

/**
 * Class represents an order
 */
class OrderTrackingNumber extends \XLite\View\ItemsList\Model\OrderTrackingNumber implements \XLite\Base\IDecorator
{
    /**
     * Inline creation mechanism position
     *
     * @return integer
     */
    protected function isInlineCreation()
    {
        return $this->getOrder() && $this->getOrder()->isNotFinishedOrder()
            ? static::CREATE_INLINE_NONE
            : parent::isInlineCreation();
    }
}
