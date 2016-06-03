<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CrispWhiteSkin\View\Sections;

/**
 * Widget class of Address section of the fastlane checkout
 *
 * @Decorator\Depend("XC\FastLaneCheckout")
 */
class Address extends \XLite\Module\XC\FastLaneCheckout\View\Sections\Address implements \XLite\Base\IDecorator
{
    /**
     * @return string
     */
    protected function getBillingFormTitle()
    {
        return static::t('Billing');
    }

    /**
     * @return string
     */
    protected function getShippingFormTitle()
    {
        return static::t('Shipping');
    }
}
