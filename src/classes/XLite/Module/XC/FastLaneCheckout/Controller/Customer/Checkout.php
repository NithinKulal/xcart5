<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\FastLaneCheckout\Controller\Customer;

use \XLite\Module\XC\FastLaneCheckout;

/**
 * Disable default one-page checkout in case of fastlane checkout
 */
class Checkout extends \XLite\Controller\Customer\Checkout implements \XLite\Base\IDecorator
{
    /**
     * Check whether the title is to be displayed in the content area
     *
     * @return boolean
     */
    public function isTitleVisible()
    {
        return !FastLaneCheckout\Main::isFastlaneEnabled();
    }

    /**
     * Get 'Terms and conditions' page URL
     *
     * @return string
     */
    public function getTermsURL()
    {
        return \XLite\Core\Config::getInstance()->General->terms_url;
    }
}
