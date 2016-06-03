<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Sort;

/**
 * Order sort widget
 *
 * @ListChild (list="orders.panel", weight="20")
 */
class Order extends \XLite\View\Sort\ASort
{

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams[self::PARAM_PARAMS]->setValue(
            array(
                'target' => 'order_list',
                'mode' => 'search',
            )
        );

        $this->widgetParams[self::PARAM_SORT_CRITERIONS]->setValue(
            array(
                'order_id' => 'Order id',
                'date'     => 'Date',
                'status'   => 'Status',
                'total'    => 'Total',
            )
        );

        $this->widgetParams[self::PARAM_CELL]->setValue(\XLite\Core\Session::getInstance()->orders_search);
    }
}
