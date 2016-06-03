<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductFilter\View\Filter;

/**
 * In stock only widget
 *
 * @ListChild (list="sidebar.filter", zone="customer", weight="200")
 */
class InStockOnly extends \XLite\Module\XC\ProductFilter\View\Filter\AFilter
{
    /**
     * Get widget templates directory
     *
     * @return string
     */
    protected function getDir()
    {
        return 'modules/XC/ProductFilter/filter/in_stock_only';
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
            && \XLite\Core\Config::getInstance()->XC->ProductFilter->enable_in_stock_only_filter;
    }

    /**
     * Get value
     *
     * @return string
     */
    protected function getValue()
    {
        $filterValues = $this->getFilterValues();

        return isset($filterValues['inStock'])
            ? $filterValues['inStock'] : '';
    }

}
