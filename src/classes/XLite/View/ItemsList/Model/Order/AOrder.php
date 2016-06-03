<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\ItemsList\Model\Order;

/**
 * Abstract order-based list
 */
abstract class AOrder extends \XLite\View\ItemsList\Model\Table
{
    /**
     * Define repository name
     *
     * @return string
     */
    protected function defineRepositoryName()
    {
        return 'XLite\Model\Order';
    }

    /**
     * Get list name suffixes
     *
     * @return array
     */
    protected function getListNameSuffixes()
    {
        return array('orders');
    }

    /**
     * Get container class
     *
     * @return string
     */
    protected function getContainerClass()
    {
        return parent::getContainerClass() . ' orders';
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
        return \XLite\Core\Converter::buildURL(
            $column[static::COLUMN_LINK],
            '',
            array('order_number' => $entity->getOrderNumber())
        );
    }
}

