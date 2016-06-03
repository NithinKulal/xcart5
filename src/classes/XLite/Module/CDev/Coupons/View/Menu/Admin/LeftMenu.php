<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Coupons\View\Menu\Admin;

/**
 * Left menu widget
 */
class LeftMenu extends \XLite\View\Menu\Admin\LeftMenu implements \XLite\Base\IDecorator
{
    /**
     * Define quick links
     *
     * @return array
     */
    protected function defineQuickLinks()
    {
        $result = parent::defineQuickLinks();
        $result['add_new'][static::ITEM_CHILDREN]['add_coupon'] = array(
            static::ITEM_TITLE      => static::t('Coupon'),
            static::ITEM_ICON_SVG   => 'images/add_product.svg',
            static::ITEM_TARGET     => 'coupon',
            static::ITEM_WEIGHT     => 400,
            static::ITEM_PERMISSION => 'manage coupons',
        );

        return $result;
    }

    /**
     * Mark selected
     *
     * @param array $items Items
     *
     * @return array
     */
    protected function markSelected($items)
    {
        if ('coupon' == $this->getTarget()) {
            $this->selectedItem = array(
                'weight' => 10,
                'index'  => 'coupons',
            );
        }

        return parent::markSelected($items);
    }
}
