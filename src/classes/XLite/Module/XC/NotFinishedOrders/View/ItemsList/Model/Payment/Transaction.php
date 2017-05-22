<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\NotFinishedOrders\View\ItemsList\Model\Payment;

/**
 * Class represents an order
 */
class Transaction extends \XLite\View\ItemsList\Model\Payment\Transaction implements \XLite\Base\IDecorator
{
    /**
     * Get order linked to the entity (payment transaction)
     *
     * @param \XLite\Model\Payment\Transaction $entity Payment transaction
     *
     * @return \XLite\Model\Order
     */
    protected function getLinkedOrder($entity)
    {
        $result = null;

        /** @var \XLite\Model\Order $order */
        $order = $this->getOrder($entity);

        if ($order) {

            if ($order->isNotFinishedOrder()) {
                $result = $order;

            } elseif ($order instanceOf \XLite\Model\Cart) {
                $result = $order->getNotFinishedOrder();
            }
        }

        return $result;
    }

    /**
     * Get order
     *
     * @param \XLite\Model\Payment\Transaction $entity Payment transaction
     *
     * @return string
     */
    protected function getOrderColumnValue(\XLite\Model\Payment\Transaction $entity)
    {
        return $this->getLinkedOrder($entity)
            ? 'View'
            : parent::getOrderColumnValue($entity);
    }

    /*
     * Check if transaction has order, which can be viewed by link
     *
     * @param \XLite\Model\AEntity $entity
     *
     * @return boolean
     */
    protected function hasLinkableOrder(\XLite\Model\AEntity $entity)
    {
        return parent::hasLinkableOrder($entity)
            || $this->getLinkedOrder($entity);
    }

    /**
     * Build entity page URL
     *
     * @param \XLite\Model\AEntity $entity Entity
     * @param array                $column Column data
     *
     * @return string
     */
    protected function buildEntityURL(\XLite\Model\AEntity $entity, array $column)
    {
        if ('order' === $column[static::COLUMN_CODE] && ($order = $this->getLinkedOrder($entity))) {
            $link = \XLite\Core\Converter::buildURL(
                $column[static::COLUMN_LINK],
                '',
                array('order_id' => $order->getOrderId())
            );

        } else {
            $link = parent::buildEntityURL($entity, $column);
        }

        return $link;
    }
}
