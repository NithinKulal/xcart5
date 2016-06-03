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
 */
class Checkout extends \XLite\View\Checkout implements \XLite\Base\IDecorator
{
    /**
     * Return list of targets allowed for this widget
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $result = parent::getAllowedTargets();

        // Disable default checkout when fastlane is enabled
        if (FastLaneCheckout\Main::isFastlaneEnabled()) {
            $result = array(
                'DO_NOT_DISPLAY'
            );
        }

        return $result;
    }
}
