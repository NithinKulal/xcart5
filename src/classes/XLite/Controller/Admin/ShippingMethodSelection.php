<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

/**
 * Shipping method selection controller
 */
class ShippingMethodSelection extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('What type of shipping rates would you like to use?');
    }

    /**
     * Return class name for the controller main form
     *
     * @return string
     */
    protected function getModelFormClass()
    {
        return 'XLite\View\Model\Shipping\Offline';
    }
}
