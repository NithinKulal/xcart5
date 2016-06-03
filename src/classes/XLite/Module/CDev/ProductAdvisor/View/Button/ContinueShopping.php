<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\ProductAdvisor\View\Button;

/**
 * "Continue shopping" button
 */
class ContinueShopping extends \XLite\View\Button\ContinueShopping implements \XLite\Base\IDecorator
{
    /**
     * Returns allowed continue shopping targets
     *
     * @return array
     */
    protected function getAllowedContinueShoppingTargets()
    {
        return array_merge(
            parent::getAllowedContinueShoppingTargets(),
            array('sale_products', 'new_arrivals', 'coming_soon')
        );
    }
}
