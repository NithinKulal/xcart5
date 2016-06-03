<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CrispWhiteSkin\View;

/**
 * Cart widget
 */
abstract class Cart extends \XLite\View\Cart implements \XLite\Base\IDecorator
{
    /**
     * Check - discount coupon subpanel is visible or not
     *
     * @param array $surcharge Surcharge
     *
     * @return boolean
     */
    protected function isShippingEstimatorVisible(array $surcharge)
    {
        return 'shipping' === strtolower($surcharge['code']);
    }
}
