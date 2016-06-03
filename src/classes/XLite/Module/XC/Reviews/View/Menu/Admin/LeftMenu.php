<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Reviews\View\Menu\Admin;

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
            $list['catalog'][static::ITEM_CHILDREN]['reviews'] = array(
                static::ITEM_TITLE  => static::t('Reviews'),
                static::ITEM_TARGET => 'reviews',
                static::ITEM_WEIGHT => 320,
            );
        }

        return $list;
    }
}
