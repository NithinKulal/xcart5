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
        $itemList = new \XLite\Module\XC\ProductFilter\View\ItemsList\Product\Customer\Category\CategoryFilter;

        $cnd = $itemList->getSearchCondition();
        $cnd->{\XLite\Model\Repo\Product::P_SCALAR_PROPERTY} = 'price';
        $cnd->{\XLite\Model\Repo\Product::P_SCALAR_FUNCTION} = 'min';

        return number_format(
            \XLite\Core\Database::getRepo('\XLite\Model\Product')->search(
                $cnd,
                \XLite\Model\Repo\Product::SEARCH_MODE_SCALAR
            ),
            \XLite::getInstance()->getCurrency()->getE(),
            '.',
            ''
        );
    }

    /**
     * Return max value
     *
     * @return float
     */
    public function getMaxPrice()
    {
        $itemList = new \XLite\Module\XC\ProductFilter\View\ItemsList\Product\Customer\Category\CategoryFilter;

        $cnd = $itemList->getSearchCondition();
        $cnd->{\XLite\Model\Repo\Product::P_SCALAR_PROPERTY} = 'price';
        $cnd->{\XLite\Model\Repo\Product::P_SCALAR_FUNCTION} = 'max';

        return number_format(
            \XLite\Core\Database::getRepo('\XLite\Model\Product')->search(
                $cnd,
                \XLite\Model\Repo\Product::SEARCH_MODE_SCALAR
            ),
            \XLite::getInstance()->getCurrency()->getE(),
            '.',
            ''
        );
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
