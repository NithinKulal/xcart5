<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\FastLaneCheckout\View\FormField\Select;

/**
 * Checkout type selector
 */
class CheckoutType extends \XLite\View\FormField\Select\Regular
{
    const TYPE_FAST_LANE = 'fast-lane';
    const TYPE_ONE_PAGE = 'one-page';

    /**
     * getDefaultOptions
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return array(
            static::TYPE_ONE_PAGE => static::t('One Page checkout'),
            static::TYPE_FAST_LANE => static::t('Fast Lane checkout'),
        );
    }
}
