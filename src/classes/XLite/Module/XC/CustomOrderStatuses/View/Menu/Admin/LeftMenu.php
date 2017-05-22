<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CustomOrderStatuses\View\Menu\Admin;

/**
 * Left menu widget
 */
class LeftMenu extends \XLite\View\Menu\Admin\LeftMenu implements \XLite\Base\IDecorator
{
    /**
     * Define items
     *
     * @return array
     */
    protected function defineItems()
    {
        $list = parent::defineItems();

        if (isset($list['sales'])) {
            $list['sales'][static::ITEM_CHILDREN]['order_statuses'] = array(
                static::ITEM_TITLE      => static::t('Order statuses'),
                static::ITEM_TARGET     => 'order_statuses',
                static::ITEM_PERMISSION => 'manage orders',
                static::ITEM_WEIGHT     => 150,
            );
        }

        return $list;
    }
}
