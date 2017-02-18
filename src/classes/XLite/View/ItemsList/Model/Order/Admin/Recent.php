<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\ItemsList\Model\Order\Admin;

/**
 * Recent orders list
 *
 * @ListChild (list="admin.center", zone="admin")
 */
class Recent extends \XLite\View\ItemsList\Model\Order\Admin\Search
{
    /**
     * Return list of targets allowed for this widget
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $result = parent::getAllowedTargets();

        $result[] = 'recent_orders';

        return $result;
    }

    /**
     * Get wrapper form target
     *
     * @return array
     */
    protected function getFormTarget()
    {
        return 'recent_orders';
    }

    /**
     * Get items count (public)
     * 
     * @return integer
     */
    public function getItemsCountPublic()
    {
        return $this->getItemsCount();
    }

    /**
     * Return title
     *
     * @return string
     */
    protected function getHead()
    {
        return null;
    }

    /**
     * Define list columns
     *
     * @return array
     */
    protected function defineColumns()
    {
        $result = parent::defineColumns();

        foreach ($result as $k => $v) {
            if (isset($v[static::COLUMN_SORT])) {
                unset($result[$k][static::COLUMN_SORT]);
            }
        }

        return $result;
    }

    /**
     * Description for blank items list
     *
     * @return string
     */
    protected function getBlankItemsListDescription()
    {
        return static::t('itemslist.admin.order.blank');
    }

    /**
     * getEmptyListTemplate
     *
     * @return string
     */
    protected function getEmptyListTemplate()
    {
        return $this->getDir() . '/' . $this->getPageBodyDir() . '/order/empty_recent_orders.twig';
    }

    /**
     * Get search condition
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getSearchCondition()
    {
        $cnd = \XLite\Core\Database::getRepo('XLite\Model\Order')->getRecentOrdersCondition();
        $cnd->{\XLite\Model\Repo\Order::P_ORDER_BY} = array(array('o.date', 'o.order_id'), array('DESC', 'DESC'));

        return $cnd;
    }
}
