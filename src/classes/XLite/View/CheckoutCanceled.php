<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

/**
 * Checkout canceled page
 *
 * @ListChild (list="center")
 */
class CheckoutCanceled extends \XLite\View\ACheckoutFailed
{
    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();
        $list[] = 'checkoutCanceled';

        return $list;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getFailedTemplate()
    {
        return 'checkout/canceled_message.twig';
    }
}
