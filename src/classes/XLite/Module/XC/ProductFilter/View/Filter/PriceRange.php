<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductFilter\View\Filter;

/**
 * Price range widget
 *
 * @ListChild (list="sidebar.filter", zone="customer", weight="100")
 */
class PriceRange extends \XLite\Module\XC\ProductFilter\View\Filter\AFilter
{
    /**
     * Return min price
     *
     * @return float
     */
    public function getMinPrice()
    {
        return number_format(
            \XLite\Core\Database::getRepo('\XLite\Model\Product')->search(
                $this->getMinPriceCondition(),
                \XLite\Model\Repo\Product::SEARCH_MODE_SCALAR
            ),
            \XLite::getInstance()->getCurrency()->getE(),
            '.',
            ''
        );
    }

    /**
     * Return min price condition
     *
     * @return float
     */
    public function getMinPriceCondition()
    {
        $itemList = new \XLite\Module\XC\ProductFilter\View\ItemsList\Product\Customer\Category\CategoryFilter;

        $cnd = $itemList->getSearchCondition();
        $profile = \XLite\Core\Auth::getInstance()->getProfile();
        $cnd->{\XLite\Model\Repo\Product::P_QUICK_DATA_MEMBERSHIP} = $profile ? $profile->getMembership() : null;
        $cnd->{\XLite\Model\Repo\Product::P_SCALAR_SELECT} = 'MIN(qdm.price)';
        $cnd->filter = null;

        return $cnd;
    }

    /**
     * Return max value
     *
     * @return float
     */
    public function getMaxPrice()
    {
        return number_format(
            \XLite\Core\Database::getRepo('\XLite\Model\Product')->search(
                $this->getMaxPriceCondition(),
                \XLite\Model\Repo\Product::SEARCH_MODE_SCALAR
            ),
            \XLite::getInstance()->getCurrency()->getE(),
            '.',
            ''
        );
    }

    /**
     * Return max price condition
     *
     * @return float
     */
    public function getMaxPriceCondition()
    {
        $itemList = new \XLite\Module\XC\ProductFilter\View\ItemsList\Product\Customer\Category\CategoryFilter;

        $cnd = $itemList->getSearchCondition();
        $profile = \XLite\Core\Auth::getInstance()->getProfile();
        $cnd->{\XLite\Model\Repo\Product::P_QUICK_DATA_MEMBERSHIP} = $profile ? $profile->getMembership() : null;
        $cnd->{\XLite\Model\Repo\Product::P_SCALAR_SELECT} = 'MAX(qdm.price)';
        $cnd->filter = null;

        return $cnd;
    }

    /**
     * Get currency symbol
     *
     * @return string
     */
    public function getSymbol()
    {
        $currency = \XLite::getInstance()->getCurrency();

        return $currency ? $currency->getCurrencySymbol() : '';
    }

    /**
     * Get widget templates directory
     *
     * @return string
     */
    protected function getDir()
    {
        return 'modules/XC/ProductFilter/filter/price_range';
    }

    /**
     * Return default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->getDir() . '/body.twig';
    }

    /**
     * Check widget visibility
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && \XLite\Core\Config::getInstance()->XC->ProductFilter->enable_price_range_filter;
    }

    /**
     * Get value
     *
     * @return array
     */
    protected function getValue()
    {
        $filterValues = $this->getFilterValues();

        return (
            isset($filterValues['price'])
            && is_array($filterValues['price'])
        ) ? $filterValues['price'] : array();
    }
}
