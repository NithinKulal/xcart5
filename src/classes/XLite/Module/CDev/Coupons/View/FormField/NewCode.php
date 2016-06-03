<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Coupons\View\FormField;

/**
 * New coupon code
 */
class NewCode extends \XLite\Module\CDev\Coupons\View\FormField\Code
{
    /**
     * Assemble classes
     *
     * @param array $classes Classes
     *
     * @return array
     */
    protected function assembleClasses(array $classes)
    {
        return array_merge(
            parent::assembleClasses($classes),
            array('not-save', 'not-significant')
        );
    }

    /**
     * Get default maximum size
     *
     * @return integer
     */
    protected function getDefaultMaxSize()
    {
        return 16;
    }
}
