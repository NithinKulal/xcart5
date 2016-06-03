<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */


namespace XLite\Module\XC\FreeShipping\Model;

/**
 * Decorate OrderItem model
 */
class OrderItem extends \XLite\Model\OrderItem implements \XLite\Base\IDecorator
{
    /**
     * Return true if order item is forced to be 'free shipping' item
     *
     * @return boolean
     */
    public function isFreeShipping()
    {
        return $this->getProduct()->getFreeShip();
    }
}
