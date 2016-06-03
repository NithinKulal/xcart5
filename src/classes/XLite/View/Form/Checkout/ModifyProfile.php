<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Form\Checkout;

/**
 * Register form (Checkout page)
 */
class ModifyProfile extends \XLite\View\Form\Checkout\ACheckout
{
    /**
     * getDefaultAction
     *
     * @return string
     */
    protected function getDefaultAction()
    {
        return 'modify_profile';
    }

    /**
     * getDefaultClassName
     *
     * @return string
     */
    protected function getDefaultClassName()
    {
        return 'profile-form';
    }
}
