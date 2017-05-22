<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\VendorMessages\View\Menu\Admin\LeftMenu\Info;

/**
 * Left side menu widget
 */
class Menu extends \XLite\View\Menu\Admin\LeftMenu\Info\Menu implements \XLite\Base\IDecorator
{
    /**
     * @inheritdoc
     */
    protected function defineItems()
    {
        $list = parent::defineItems();
        $list['messages'] = array(
            static::ITEM_WEIGHT => 1000,
            static::ITEM_WIDGET => 'XLite\Module\XC\VendorMessages\View\Menu\Admin\LeftMenu\Info\Messages',
        );

        return $list;
    }
}
