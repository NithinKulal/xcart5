<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CrispWhiteSkin\View;

/**
 * Mobile filters button
 *
 * @Decorator\Depend ("XC\ProductFilter")
 *
 * @ListChild (list="itemsList.product.grid.customer.header", weight="40", zone="customer")
 * @ListChild (list="itemsList.product.list.customer.header", weight="40", zone="customer")
 * @ListChild (list="itemsList.product.table.customer.header", weight="40", zone="customer")
 */
class FiltersMobile extends \XLite\View\AView
{
    /**
     * Widget parameter names
     */
    const PARAM_CATEGORY_ID = 'category_id';
    const PARAM_AJAX_EVENTS = 'ajax_events';

    /**
     * Return list of targets allowed for this widget
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $result = parent::getAllowedTargets();
        $result[] = 'category';
        $result[] = 'category_filter';

        return $result;
    }

    /**
     * Return default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/XC/ProductFilter/filters_mobile.twig';
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        $result = parent::isVisible()
            && \XLite\Core\MobileDetect::getInstance()->isMobile()
            && $this->getCategory()
            && 1 < $this->getCategory()->getProductsCount();

        if ($result) {
            $config = \XLite\Core\Config::getInstance()->XC->ProductFilter;
            $result = $config->enable_in_stock_only_filter
                || $config->enable_price_range_filter;

            if (!$result
                && $config->enable_attributes_filter
            ) {
                $filterAttributes = new \XLite\Module\XC\ProductFilter\View\Filter\Attributes;

                $result = $filterAttributes->isVisible();

            }
        }

        return $result;
    }

    /**
     * Get requested category object
     *
     * @return \XLite\Model\Category
     */
    protected function getCategory()
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Category')->find($this->getCategoryId());
    }

    /**
     * Get requested category ID
     *
     * @return integer
     */
    protected function getCategoryId()
    {
        return \XLite\Core\Request::getInstance()->category_id;
    }

    /**
     * Get cache parameters
     *
     * @return array
     */
    protected function getCacheParameters()
    {
        $list = parent::getCacheParameters();
        $list[] = $this->getCategoryId();

        return $list;
    }

    /**
     * This data will be accessible using JS core.getCommentedData() method.
     *
     * @return array
     */
    protected function getCommentedData()
    {
        return array(
            'widgetParams' => array(
                static::PARAM_CATEGORY_ID => $this->getCategoryId(),
                static::PARAM_AJAX_EVENTS => $this->isFilterTarget(),
            )
        );
    }

    /**
     * Returns true if current target is category_filter
     *
     * @return string
     */
    protected function isFilterTarget()
    {
        return in_array($this->getTarget(), array('category', 'category_filter'), true);
    }

    /**
     * Check if sidebar with filter is visible
     *
     * @return boolean
     */
    public function isSidebarVisible()
    {

        return !$this->isAJAX()
            && (\XLite\Core\Layout::getInstance()->isSidebarFirstVisible()
            || \XLite\Core\Layout::getInstance()->isSidebarSecondVisible());
    }

    /**
     * Register the CSS classes for this block
     *
     * @return string
     */
    protected function getBlockClasses()
    {
        return parent::getBlockClasses() . ' block-product-filter';
    }
}
