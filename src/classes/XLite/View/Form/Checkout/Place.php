<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Form\Checkout;

/**
 * Place order form
 */
class Place extends \XLite\View\Form\Checkout\ACheckout
{
    /**
     * JavaScript: this value will be returned on form submit
     * NOTE - this function designed for AJAX easy switch on/off
     *
     * @return string
     */
    protected function getOnSubmitResult()
    {
        return 'false';
    }

    /**
     * getDefaultAction
     *
     * @return string
     */
    protected function getDefaultAction()
    {
        return 'checkout';
    }

}
