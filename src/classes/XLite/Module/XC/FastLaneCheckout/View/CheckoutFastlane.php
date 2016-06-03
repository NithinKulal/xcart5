<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\FastLaneCheckout\View;

use \XLite\Module\XC\FastLaneCheckout;

/**
 * Disable default one-page checkout in case of fastlane checkout
 *
 * @ListChild (list="center")
 */
class CheckoutFastlane extends \XLite\View\AView
{
    /**
     * Return list of targets allowed for this widget
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $result = parent::getAllowedTargets();

        if (FastLaneCheckout\Main::isFastlaneEnabled()) {
            $result[] = 'checkout';
        } else {
            $result = array(
                'DO_NOT_DISPLAY'
            );
        }

        return $result;
    }

    /**
     * Return default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return FastLaneCheckout\Main::getSkinDir() . 'checkout_fastlane/template.twig';
    }
}
