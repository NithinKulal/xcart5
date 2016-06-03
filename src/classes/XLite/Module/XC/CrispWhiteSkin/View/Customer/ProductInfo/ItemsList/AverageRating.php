<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CrispWhiteSkin\View\Customer\ProductInfo\ItemsList;

/**
 * Reviews list widget
 *
 * @Decorator\Depend ("XC\Reviews")
 */
abstract class AverageRating extends \XLite\Module\XC\Reviews\View\Customer\ProductInfo\ItemsList\AverageRating implements \XLite\Base\IDecorator
{
    /**
     * Return TRUE if customer can rate product
     *
     * @return boolean
     */
    public function isAllowedRateProduct()
    {
        return false;
    }
}
