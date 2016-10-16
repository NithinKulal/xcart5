<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\Amazon\PayWithAmazon\View;

/**
 * Amazon checkout widget
 *
 * @ListChild (list="center.top", zone="customer", weight="100")
 */
class CheckoutButton extends \XLite\View\AView
{
    /**
     * @return array|\string[]
     */
    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();
        $list[] = 'checkout';

        return $list;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/Amazon/PayWithAmazon/checkout_button/checkout.twig';
    }

    /**
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible() && $this->isCheckoutAvailable();
    }
}
