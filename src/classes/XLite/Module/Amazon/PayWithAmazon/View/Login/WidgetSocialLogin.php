<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\Amazon\PayWithAmazon\View\Login;

/**
 * Social sign-in widget
 *
 * @Decorator\Depend ({"Amazon\PayWithAmazon", "CDev\SocialLogin"})
 */
class WidgetSocialLogin extends \XLite\Module\Amazon\PayWithAmazon\View\Login\Widget implements \XLite\Base\IDecorator
{
    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return false;
    }
}
