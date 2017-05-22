<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\NotFinishedOrders\Model;

/**
 * Class represents an order
 */
class Order extends \XLite\Model\Order implements \XLite\Base\IDecorator
{
    const TTL_DAY_SECONDS = 86400;

    /**
     * Order link to not finished order
     *
     * @var \XLite\Model\Order
     *
     * @OneToOne   (targetEntity="XLite\Model\Order", cascade={"merge", "detach", "remove"})
     * @JoinColumn (name="not_finished_order_id", referencedColumnName="order_id", onDelete="SET NULL")
     */
    protected $not_finished_order;

    /**
     * Check if the cart has only failed transaction
     *
     * @return boolean
     */
    public function isNotFinishedOrder()
    {
        return $this instanceof \XLite\Model\Cart
            && $this->getShippingStatusCode() === \XLite\Model\Order\Status\Shipping::STATUS_NOT_FINISHED;
    }

    /**
     * Return true if payment method is required for the order
     *
     * @return boolean
     */
    protected function isPaymentMethodRequired()
    {
        return parent::isPaymentMethodRequired()
            && !($this->isNotFinishedOrder() && $this->getNotFinishedOrder());
    }

    /**
     * Return printable order number
     *
     * @return string
     */
    public function getPrintableOrderNumber()
    {
        return $this->isNotFinishedOrder()
            ? ''
            : parent::getPrintableOrderNumber();
    }

    /**
     * Check if not finished order is not longer usable
     *
     * @return boolean
     */
    public function isExpiredTTL()
    {
        return $this->isNotFinishedOrder()
            && $this->getNotFinishedOrderTTL() !== null
            && ($this->getLastRenewDate() + $this->getNotFinishedOrderTTL()) < \XLite\Core\Converter::time();
    }

    /**
     * Return cart TTL
     *
     * Is set in module configuration. Defaults to one week.
     *
     * @return integer|null
     */
    protected function getNotFinishedOrderTTL()
    {
        return \XLite\Core\Config::getInstance()->XC->NotFinishedOrders->limit_nf_order_ttl
            ? ((int) \XLite\Core\Config::getInstance()->XC->NotFinishedOrders->nf_order_ttl) * self::TTL_DAY_SECONDS
            : null;
    }

    /**
     * A "change status" handler
     *
     * @return void
     */
    protected function processDecline()
    {
        $previous = $this->isIgnoreCustomerNotifications();
        if ($this->isNotFinishedOrder()) {
            $this->setIgnoreCustomerNotifications(true);
        }

        parent::processDecline();

        $this->setIgnoreCustomerNotifications($previous);
    }

    /**
     * Get paid totals
     *
     * @return array
     */
    public function getPaidTotals()
    {
        $totals = parent::getPaidTotals();

        $nfo = $this->getNotFinishedOrder();

        if ($nfo) {
            $totalsNFO = $nfo->getPaidTotals();
            $totals['total'] += $totalsNFO['total'];
            $totals['totalAsSurcharges'] += $totalsNFO['totalAsSurcharges'];
        }

        return $totals;
    }

    /**
     * Set not_finished_order
     *
     * @param \XLite\Model\Order $notFinishedOrder
     * @return Order
     */
    public function setNotFinishedOrder(\XLite\Model\Order $notFinishedOrder = null)
    {
        $this->not_finished_order = $notFinishedOrder;
        return $this;
    }

    /**
     * Get not_finished_order
     *
     * @return \XLite\Model\Order 
     */
    public function getNotFinishedOrder()
    {
        return $this->not_finished_order;
    }
}
