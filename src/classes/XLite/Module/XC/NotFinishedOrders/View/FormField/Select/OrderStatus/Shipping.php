<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\NotFinishedOrders\View\FormField\Select\OrderStatus;

/**
 * Shipping order status selector
 */
class Shipping extends \XLite\View\FormField\Select\OrderStatus\Shipping implements \XLite\Base\IDecorator
{
    /**
     * Not finished status cache
     *
     * @var \XLite\Model\Order\Status\Shipping
     */
    protected $notFinishedStatus;

    /**
     * Check - specified option is disabled or not
     *
     * @param mixed $value Option value
     *
     * @return boolean
     */
    protected function getNotFinishedStatus()
    {
        if (null === $this->notFinishedStatus) {
            $this->notFinishedStatus = \XLite\Core\Database::getRepo('XLite\Model\Order\Status\Shipping')
                ->findOneByCode(\XLite\Model\Order\Status\Shipping::STATUS_NOT_FINISHED);
        }

        return $this->notFinishedStatus;
    }

    /**
     * Check - specified option is disabled or not
     *
     * @param mixed $value Option value
     *
     * @return boolean
     */
    protected function isOptionDisabled($value)
    {
        return parent::isOptionDisabled($value)
            || ($this->getNotFinishedStatus() && $this->getNotFinishedStatus()->getId() === $value);
    }
}
