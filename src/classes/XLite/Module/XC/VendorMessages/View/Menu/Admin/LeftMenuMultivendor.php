<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\VendorMessages\View\Menu\Admin;

/**
 * Left side menu widget
 *
 * @Decorator\After ("XC\VendorMessages")
 * @Decorator\Depend ("XC\MultiVendor")
 */
class LeftMenuMultivendor extends \XLite\View\Menu\Admin\LeftMenu implements \XLite\Base\IDecorator
{
    /**
     * @inheritdoc
     */
    protected function defineItems()
    {
        $items = parent::defineItems();
        if (isset($items['sales'][static::ITEM_CHILDREN]['messages']) && \XLite\Core\Auth::getInstance()->isVendor()) {
            if (\XLite\Module\XC\VendorMessages\Main::isVendorAllowed()) {
                $items['sales'][static::ITEM_CHILDREN]['messages'][static::ITEM_PERMISSION] = '[vendor] manage orders';

            } else {
                unset($items['sales'][static::ITEM_CHILDREN]['messages']);
            }
        }

        return $items;
    }
}
