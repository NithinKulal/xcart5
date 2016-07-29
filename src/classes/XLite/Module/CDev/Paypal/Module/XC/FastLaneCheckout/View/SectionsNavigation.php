<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\Module\XC\FastLaneCheckout\View;

/**
 * Disable default one-page checkout in case of fastlane checkout
 *
 * @Decorator\Depend("XC\FastLaneCheckout")
 */
class SectionsNavigation extends \XLite\Module\XC\FastLaneCheckout\View\SectionsNavigation implements \XLite\Base\IDecorator
{
    /**
     * Defines the additional data array
     *
     * @return array
     */
    protected function defineWidgetData()
    {
        $data = parent::defineWidgetData();

        if ($this->isReturnedAfterExpressCheckout()) {
            $data = array_merge(
                $data,
                array(
                    'start_with' => 'payment'
                )
            );
        }

        return $data;
    }
}
