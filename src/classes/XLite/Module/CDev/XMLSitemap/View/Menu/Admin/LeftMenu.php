<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\XMLSitemap\View\Menu\Admin;

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
        $items = parent::defineItems();
   
        if (isset($items['store_setup'])
            && isset($items['store_setup'][static::ITEM_CHILDREN])
        ) {
            $items['store_setup'][static::ITEM_CHILDREN]['sitemap'] = array(
                static::ITEM_TITLE  => static::t('XML sitemap'),
                static::ITEM_TARGET => 'sitemap',
                static::ITEM_WEIGHT => 2000,
            );
        }

        return $items;
    }
}
