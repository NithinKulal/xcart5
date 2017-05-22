<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\NotFinishedOrders\View\ItemsList\Model\Order\Admin;

/**
 * Class represents an order
 */
class Search extends \XLite\View\ItemsList\Model\Order\Admin\Search implements \XLite\Base\IDecorator
{
    /**
     * Get a list of CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/XC/NotFinishedOrders/items_list/model/table/order/style.css';

        return $list;
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
        if ($entity->isNotFinishedOrder()) {
            $link = \XLite\Core\Converter::buildURL(
                $column[static::COLUMN_LINK],
                '',
                array('order_id' => $entity->getOrderId())
            );

        } else {
            $link = parent::buildEntityURL($entity, $column);
        }

        return $link;
    }

    /**
     * Preprocess order number
     *
     * @param integer              $orderNumber Order number
     * @param array                $column      Column data
     * @param \XLite\Model\Order   $entity      Order
     *
     * @return string
     */
    protected function preprocessOrderNumber($orderNumber, array $column, \XLite\Model\Order $entity)
    {
        return $entity->isNotFinishedOrder()
            ? 'View'
            : parent::preprocessOrderNumber($orderNumber, $column, $entity);
    }

    /**
     * Define line class as list of names
     *
     * @param integer              $index  Line index
     * @param \XLite\Model\AEntity $entity Line model OPTIONAL
     *
     * @return array
     */
    protected function defineLineClass($index, \XLite\Model\AEntity $entity = null)
    {
        $result = parent::defineLineClass($index, $entity);

        if ($entity->isNotFinishedOrder()) {
            $result[] = 'not-finished-order';
        }

        return $result;
    }
}
