<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\XPaymentsConnector\View\ItemsList\Model\Order;

/**
 * Order item. Fix for orders with no items 
 */
class OrderItem extends \XLite\View\ItemsList\Model\OrderItem implements \XLite\Base\IDecorator 
{
    /**
     * Entity if it doesn't exist
     *
     * @return \XLite\Model\OrderItem
     */
    public function getEntity()
    {
        if (!$this->entity) {

            $this->entity = new \XLite\Model\OrderItem;

        }
        return $this->entity;
    }

}
