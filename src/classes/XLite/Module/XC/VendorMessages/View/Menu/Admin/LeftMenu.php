<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\VendorMessages\View\Menu\Admin;

/**
 * Left side menu widget
 */
class LeftMenu extends \XLite\View\Menu\Admin\LeftMenu implements \XLite\Base\IDecorator
{
    /**
     * @inheritdoc
     */
    protected function defineItems()
    {
        $items = parent::defineItems();
        $items['sales'][static::ITEM_CHILDREN]['messages'] = array(
            static::ITEM_TITLE      => static::t('Messages'),
            static::ITEM_TARGET     => 'messages',
            static::ITEM_PERMISSION => 'manage orders',
            static::ITEM_WEIGHT     => 1000,
        );

        return $items;
    }
}
