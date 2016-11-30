<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductFilter\View;

/**
 * Product comparison widget
 *
 * @ListChild (list="sidebar.single", zone="customer", weight="110")
 * @ListChild (list="sidebar.first", zone="customer", weight="110")
 */
class Filter extends \XLite\View\SideBarBox
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
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = $this->getDir() . '/style.css';

        return $list;
    }

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = $this->getDir() . '/script.js';

        return $list;
    }

    /**
     * Get title
     *
     * @return string
     */
    protected function getHead()
    {
        return 'Shopping options';
    }

    /**
     * Get widget templates directory
     *
     * @return string
     */
    protected function getDir()
    {
        return 'modules/XC/ProductFilter/sidebar';
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
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        $result = parent::isVisible()
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
        return array_merge(
            array(
                'widgetParams' => array(
                    static::PARAM_CATEGORY_ID => $this->getCategoryId(),
                    static::PARAM_AJAX_EVENTS => $this->isFilterTarget(),
                )
            ),
            $this->getJSData()
        );
    }

    /**
     * Defines if the widget is listening to #hash changes
     *
     * @return boolean
     */
    protected function getListenToHash()
    {
        return true;
    }

    /**
     * Defines the #hash prefix of the data for the widget
     *
     * @return string
     */
    protected function getListenToHashPrefix()
    {
        return 'product.category';
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
     * Register the CSS classes for this block
     *
     * @return string
     */
    protected function getBlockClasses()
    {
        return parent::getBlockClasses() . ' block-product-filter';
    }
}
