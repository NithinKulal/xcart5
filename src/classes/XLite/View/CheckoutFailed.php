<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

/**
 * Checkout failed page
 *
 * @ListChild (list="center")
 */
class CheckoutFailed extends \XLite\View\ACheckoutFailed
{
    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();
        $list[] = 'checkoutFailed';

        return $list;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getFailedTemplate()
    {
        return 'checkout/failed_message.twig';
    }
}
