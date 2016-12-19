<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\GoogleAnalytics\Module\XC\FastLaneCheckout\View;

use XLite\Module\XC\FastLaneCheckout;

/**
 * Class AView
 *
 * @Decorator\Depend("XC\FastLaneCheckout")
 */
class CheckoutFastlane extends \XLite\Module\XC\FastLaneCheckout\View\CheckoutFastlane implements \XLite\Base\IDecorator
{
    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        if (\XLite\Module\CDev\GoogleAnalytics\Main::isECommerceEnabled()
            && FastLaneCheckout\Main::isFastlaneEnabled()
        ) {
            $list[] = 'modules/CDev/GoogleAnalytics/universal/action/ecommerce/ga-ec-checkout-fastlane.js';
        }

        return $list;
    }

}
