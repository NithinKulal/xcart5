<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Form\Checkout;

/**
 * Continue as guest form (Checkout page)
 */
class ContinueAsGuest extends \XLite\View\Form\Checkout\ACheckout
{
    /**
     * getDefaultAction
     *
     * @return string
     */
    protected function getDefaultAction()
    {
        return 'update_profile';
    }

    /**
     * Required form parameters
     *
     * @return array
     */
    protected function getCommonFormParams()
    {
        $list = parent::getCommonFormParams();
        $list['returnURL'] = $this->buildURL('checkout', 'update_profile');
        $list['same_address'] = '1';

        return $list;
    }
}
