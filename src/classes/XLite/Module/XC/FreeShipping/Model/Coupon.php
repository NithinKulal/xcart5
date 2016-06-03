<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */


namespace XLite\Module\XC\FreeShipping\Model;

/**
 * Decorate Coupon model (CDev\Coupons module)
 *
 * @Decorator\Depend("CDev\Coupons")
 */
class Coupon extends \XLite\Module\CDev\Coupons\Model\Coupon implements \XLite\Base\IDecorator
{
    const TYPE_FREESHIP = 'S';

    /**
     * Get amount
     *
     * @param \XLite\Model\Order $order Order
     *
     * @return float
     */
    public function getAmount(\XLite\Model\Order $order)
    {
        return $this->isFreeShipping() ? 0 : parent::getAmount($order);
    }

    /**
     * Return true if coupon has 'Free shipping' type
     *
     * @return boolean
     */
    public function isFreeShipping()
    {
        return static::TYPE_FREESHIP == $this->getType();
    }

    /**
     * Get public name
     *
     * @param \XLite\Model\Order $order Order
     *
     * @return float
     */
    public function getPublicCode()
    {
        $result = parent::getPublicCode();

        if ($this->isFreeShipping()) {
            $result = sprintf('%s (%s)', $result, static::t('Free shipping'));
        }

        return $result;
    }

}
