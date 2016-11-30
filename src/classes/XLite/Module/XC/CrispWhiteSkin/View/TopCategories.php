<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CrispWhiteSkin\View;

/**
 * TopCategories decorator
 */
abstract class TopCategories extends \XLite\View\TopCategories implements \XLite\Base\IDecorator
{
    /**
     * Return list of disallowed targets
     *
     * @return string[]
     */
    public static function getDisallowedTargets()
    {
        return [
            'order_list',
            'address_book',
            'mailchimp_subscriptions',
            'profile',
            'messages',
            'login',
            'recover_password',
        ];
    }
}
