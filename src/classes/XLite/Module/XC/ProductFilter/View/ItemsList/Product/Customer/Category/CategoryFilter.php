<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductFilter\View\ItemsList\Product\Customer\Category;

use XLite\View\CacheableTrait;

/**
 * Category filters list widget
 *
 * @ListChild (list="center.bottom", zone="customer", weight="200")
 */
class CategoryFilter extends \XLite\View\ItemsList\Product\Customer\Category\ACategory
{
    use CacheableTrait;

    /**
     * Widget parameter names
     */
    const PARAM_FILTER = 'filter';

    /**
     * Items count before filter
     *
     * @var integer
     */
    protected $itemsCountBefore;

    /**
     * Return list of targets allowed for this widget
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        return array('category', 'category_filter');
    }

    /**
     * Get session cell name for the certain list items widget
     *
     * @return string
     */
    public static function getSessionCellName()
    {
        return parent::getSessionCellName()
            . \XLite\Core\Request::getInstance()->{self::PARAM_CATEGORY_ID};
    }

    /**
     * Return target to retrieve this widget from AJAX
     *
     * @return string
     */
    protected static function getWidgetTarget()
    {
        return 'category';
    }

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/XC/ProductFilter/category_filter/style.css';

        return $list;
    }

    /**
     * Return params list to use for search
     *
     * @return \XLite\Core\CommonCell
     */
    public function getSearchCondition()
    {
        $cnd = parent::getSearchCondition() ?: new \XLite\Core\CommonCell();

        $cnd->{\XLite\Model\Repo\Product::P_CATEGORY_ID} = $this->getCategoryId();

        return $cnd;
    }

    /**
     * Returns CSS classes for the container element
     *
     * @return string
     */
    public function getListCSSClasses()
    {
        return parent::getListCSSClasses() . ' filtered-products';
    }

    /**
     * Return number of items in products list before filter
     *
     * @return array
     */
    protected function getItemsCountBefore()
    {
        if (null === $this->itemsCountBefore) {
            $this->itemsCountBefore = parent::getData($this->getSearchCondition(), true);
        }

        return $this->itemsCountBefore;
    }

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            self::PARAM_FILTER => new \XLite\Model\WidgetParam\TypeCollection('Product filter', array()),
        );
    }

    /**
     * Define so called "request" parameters
     *
     * @return void
     */
    protected function defineRequestParams()
    {
        parent::defineRequestParams();

        $this->requestParams[] = self::PARAM_FILTER;
    }

    /**
     * @inheritdoc
     */
    public static function getSearchParams()
    {
        return array_merge(
            parent::getSearchParams(),
            [
                'filter' => static::PARAM_FILTER,
            ]
        );
    }

    /**
     * Get search values storage
     *
     * @param boolean $forceFallback Force fallback to session storage
     *
     * @return \XLite\View\ItemsList\ISearchValuesStorage
     */
    public static function getSearchValuesStorage($forceFallback = false)
    {
        $requestData = \XLite\Core\Request::getInstance()->getData();
        
        return new \XLite\View\ItemsList\RequestSearchValuesStorage(
            $requestData
        );
    }

    /**
     * Check if header is visible
     *
     * @return boolean
     */
    protected function isHeaderVisible()
    {
        return $this->hasResults();
    }

    /**
     * Check if pager is visible
     *
     * @return boolean
     */
    protected function isPagerVisible()
    {
        return $this->hasResults();
    }

    /**
     * Get empty list template
     *
     * @return string
     */
    protected function getEmptyListTemplate()
    {
        return $this->getParam(self::PARAM_FILTER)
            ? 'modules/XC/ProductFilter/category_filter/empty.twig'
            : parent::getEmptyListTemplate();
    }

    /**
     * Mark list as switchable (enable / disable)
     *
     * @return boolean
     */
    protected function isDisplayWithEmptyList()
    {
        return true;
    }

    /**
     * Get cache parameters
     *
     * @return array
     */
    protected function getCacheParameters()
    {
        $list = parent::getCacheParameters();

        $list[] = md5(serialize($this->getParam(self::PARAM_FILTER)));

        return $list;
    }
}
