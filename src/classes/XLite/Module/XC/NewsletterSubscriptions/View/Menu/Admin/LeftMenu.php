<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\NewsletterSubscriptions\View\Menu\Admin;

/**
 * Top menu widget
 */
abstract class LeftMenu extends \XLite\View\Menu\Admin\LeftMenu implements \XLite\Base\IDecorator
{
    /**
     * Define items
     *
     * @return array
     */
    protected function defineItems()
    {
        $return = parent::defineItems();

        $return['promotions'][self::ITEM_CHILDREN]['newsletter_subscribers'] = array(
            self::ITEM_TITLE      => 'Subscribers',
            self::ITEM_TARGET     => 'newsletter_subscribers',
            self::ITEM_CLASS      => 'subscribers',
            self::ITEM_PERMISSION => 'manage users',
            self::ITEM_WEIGHT     => 1100,
        );

        return $return;
    }
}
