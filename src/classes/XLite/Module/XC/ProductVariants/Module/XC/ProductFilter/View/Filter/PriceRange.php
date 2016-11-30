<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductVariants\Module\XC\ProductFilter\View\Filter;

/**
 * Price range widget
 *
 * @Decorator\Depend("XC\ProductVariants")
 */
class PriceRange extends \XLite\Module\XC\ProductFilter\View\Filter\PriceRange implements \XLite\Base\IDecorator
{
    const PARAM_QUICK_DATA = 'quickData';

    /**
     * Return min price condition
     *
     * @return float
     */
    public function getMinPriceCondition()
    {
        $cnd = parent::getMinPriceCondition();

        if (\XLite\Module\XC\ProductVariants\Main::isDisplayPriceAsRange()) {
            $cnd->{\XLite\Model\Repo\Product::P_SCALAR_SELECT} = 'MIN(LEAST(qdm.price, qdm.minPrice))';
        }

        return $cnd;
    }

    /**
     * Return max price condition
     *
     * @return float
     */
    public function getMaxPriceCondition()
    {
        $cnd = parent::getMaxPriceCondition();

        if (\XLite\Module\XC\ProductVariants\Main::isDisplayPriceAsRange()) {
            $cnd->{\XLite\Model\Repo\Product::P_SCALAR_SELECT} = 'MAX(GREATEST(qdm.price, qdm.maxPrice))';
        }

        return $cnd;
    }
}