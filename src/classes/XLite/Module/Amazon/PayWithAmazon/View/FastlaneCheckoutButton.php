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
 * @Decorator\Depend({"Amazon\PayWithAmazon", "XC\FastLaneCheckout"})
 */
abstract class FastlaneCheckoutButton extends \XLite\Module\Amazon\PayWithAmazon\View\CheckoutButton implements \XLite\Base\IDecorator
{
    /**
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible() && !\XLite\Module\XC\FastLaneCheckout\Main::isFastlaneEnabled();
    }
}
