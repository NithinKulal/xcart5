<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\Amazon\PayWithAmazon\View\Menu\Customer;

/**
 * Hide top selector for amazon checkout
 */
class Top extends \XLite\View\Menu\Customer\Top implements \XLite\Base\IDecorator
{
    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return \XLite::getController()->getTarget() === 'amazon_checkout'
            ? false
            : parent::isVisible();
    }
}
