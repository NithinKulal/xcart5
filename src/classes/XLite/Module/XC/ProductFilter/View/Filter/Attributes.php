<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductFilter\View\Filter;

use XLite\View\CacheableTrait;

/**
 * Attributes widget
 *
 * @ListChild (list="sidebar.filter", zone="customer", weight="300")
 */
class Attributes extends AFilter
{
    use CacheableTrait;

    /**
     * Product classes
     *
     * @var array
     */
    protected $productClasses;

    /**
     * Get active product classes
     *
     * @return array
     */
    public function getProductClasses()
    {
        if (!isset($this->productClasses)) {
            $category = $this->getCategory();
            switch ($category->getUseClasses()) {
                case $category::USE_CLASSES_NO:
                    $this->productClasses = array();
                    break;

                case $category::USE_CLASSES_DEFINE:
                    $this->productClasses = $category->getProductClasses();
                    break;

                default:
                    $iList = new \XLite\Module\XC\ProductFilter\View\ItemsList\Product\Customer\Category\CategoryFilter;
                    $this->productClasses = \XLite\Core\Database::getRepo('XLite\Model\Product')
                        ->findFilteredProductClasses($iList->getSearchCondition());
            }
        }

        return $this->productClasses;
    }

    /**
     * Has global filtered attributes flag
     *
     * @return boolean
     */
    protected function hasGlobalFilteredAttributes()
    {
        $cnd = new \XLite\Core\CommonCell;

        $cnd->productClass = null;
        $cnd->product = null;
        $cnd->type = \XLite\Model\Attribute::getFilteredTypes();

        return 0 < \XLite\Core\Database::getRepo('\XLite\Model\Attribute')->search($cnd, true);
    }

    /**
     * Get global groups
     *
     * @return mixed
     */
    protected function getGlobalGroups()
    {
        return \XLite\Core\Database::getRepo('XLite\Model\AttributeGroup')->findByProductClass(null);
    }

    /**
     * Get widget templates directory
     *
     * @return string
     */
    protected function getDir()
    {
        return 'modules/XC/ProductFilter/filter/attributes';
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
     * Filter has at least one productClass to show
     *
     * @return boolean
     */
    protected function hasVisibleProductClasses()
    {
        $result = false;

        foreach ($this->getProductClasses() as $productClass) {
            if ($productClass->hasNonEmptyAttributes()
                || $productClass->hasNonEmptyGroups()
            ) {
                $result = true;
                break;
            }
        }

        return $result;
    }

    /**
     * Check widget visibility
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && \XLite\Core\Config::getInstance()->XC->ProductFilter->enable_attributes_filter
            && (
                (0 < count($this->getProductClasses()) && $this->hasVisibleProductClasses())
                || $this->hasGlobalFilteredAttributes()
            );
    }

    /**
     * Get cache parameters
     *
     * @return array
     */
    protected function getCacheParameters()
    {
        $cacheParams = parent::getCacheParameters();

        $cacheParams[] = $this->getCategory()->getId();
        $cacheParams[] = serialize($this->getFilterValues());

        return $cacheParams;
    }
}
