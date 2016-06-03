<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\UpdateInventory\View\Menu\Admin;

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

        if (isset($list['catalog'])) {
            $list['catalog'][static::ITEM_CHILDREN][\XLite\Module\XC\UpdateInventory\Main::TARGET_UPDATE_INVENTORY] = array(
                static::ITEM_TITLE      => static::t('Update quantity'),
                static::ITEM_TARGET     => \XLite\Module\XC\UpdateInventory\Main::TARGET_UPDATE_INVENTORY,
                static::ITEM_PERMISSION => 'manage import',
                static::ITEM_WEIGHT     => 450,
            );
        }

        return $list;
    }
}
