<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-2016 Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\QSL\CloudSearch\View\ItemsList\Product\Customer;

use XLite\Core\CommonCell;
use XLite\Core\Database;
use XLite\Module\QSL\CloudSearch\Model\Repo\Product as ProductRepo;
use XLite\Model\WidgetParam\TypeCollection;
use XLite\View\Controller;
use XLite\View\Product\ListItem;


trait FilterWithCloudSearchTrait
{
    /**
     * Override initView to pass search conditions to FiltersBox
     *
     * @return void
     */
    protected function initView()
    {
        parent::initView();

        $cnd = $this->getCloudSearchConditions();

        if ($this->isFilteringWithCloudSearch($cnd)) {
            Controller::showCloudFilters($cnd, $this->isAsynchronouslyFilteringWithCloudSearch($cnd));
        }
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
     * Mark list as switchable (enable / disable)
     *
     * @return boolean
     */
    protected function isDisplayWithEmptyList()
    {
        return true;
    }

    /**
     * Get empty list template
     *
     * @return string
     */
    protected function getEmptyListTemplate()
    {
        return $this->getParam(self::PARAM_CLOUD_FILTERS)
            ? $this->getEmptyFilteredListTemplate()
            : parent::getEmptyListTemplate();
    }

    /**
     * A flag indicating that getItemsCount should not call getData but rather return a fake PHP_INT_MAX value. This is to avoid infinite loop in getData when calling getLimitCondition which in turn calls getPager -> getItemsCount -> getData -> getLimitCondition ...
     * Effectively this enables us to create a pager with fake PHP_INT_MAX items but correct startItem and itemsPerPage. These are needed to perform CloudSearch search request with correct offset and limit parameters.
     *
     * @var bool
     */
    protected $returnIntMaxItemCount = false;

    /**
     * Return number of items in products list
     *
     * @return array
     */
    protected function getItemsCount()
    {
        return $this->returnIntMaxItemCount
            ? PHP_INT_MAX
            : parent::getItemsCount();
    }

    /**
     * Override getData to include limits in getCloudSearchResults call to avoid unnecessary calls later
     *
     * @param CommonCell $cnd       Search condition
     * @param boolean    $countOnly Return items list or only its size OPTIONAL
     *
     * @return array|integer
     */
    protected function getData(CommonCell $cnd, $countOnly = false)
    {
        if ($countOnly && $this->isLoadingWithCloudSearch($cnd)) {
            /** @var ProductRepo $repo */
            $repo = Database::getRepo('XLite\Model\Product');

            $this->returnIntMaxItemCount = true;

            $cnd = $this->getCloudSearchConditions();

            $this->returnIntMaxItemCount = false;

            $results = $repo->getCloudSearchResults($cnd);

            if ($results !== null) {
                return $results['numFoundProducts'];
            }
        }

        return parent::getData($cnd, $countOnly);
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
            self::PARAM_CLOUD_FILTERS => new TypeCollection('Cloud filters', []),
        );
    }

    /**
     * Get cache parameters
     *
     * @return array
     */
    protected function getCacheParameters()
    {
        $list = parent::getCacheParameters();

        $list[] = md5(serialize($this->getParam(self::PARAM_CLOUD_FILTERS)));

        return $list;
    }

    /**
     * Return params list to use for search
     *
     * @return \XLite\Core\CommonCell
     */
    public function getSearchCondition()
    {
        $cnd = parent::getSearchCondition();

        if ($this->isLoadingWithCloudSearch($cnd)) {
            $cnd->{ProductRepo::P_LOAD_PRODUCTS_WITH_CLOUD_SEARCH} = true;
        }

        return $cnd;
    }

    /**
     * Return search parameters.
     *
     * @return array
     */
    public static function getSearchParams()
    {
        return parent::getSearchParams() + [
            ProductRepo::P_CLOUD_FILTERS                   => self::PARAM_CLOUD_FILTERS,
            ProductRepo::P_LOAD_PRODUCTS_WITH_CLOUD_SEARCH => self::PARAM_LOAD_PRODUCTS_WITH_CLOUD_SEARCH,
        ];
    }

    /**
     * Get current search condition to be used in CloudSearch searching and filtering
     *
     * @return CommonCell
     */
    protected function getCloudSearchConditions()
    {
        return $this->getLimitCondition();
    }

    /**
     * Get product list item widget params required for the widget of type getProductWidgetClass().
     *
     * @param \XLite\Model\Product $product
     *
     * @return array
     */
    protected function getProductWidgetParams(\XLite\Model\Product $product)
    {
        $params = parent::getProductWidgetParams($product);

        if (defined('XLite\View\Product\ListItem::PARAM_CLOUD_FILTERS_FILTER_VARIANTS')) {
            $cnd = $this->getCloudSearchConditions();

            if (
                $this->isLoadingWithCloudSearch($cnd)
                && !empty($cnd->{ProductRepo::P_CLOUD_FILTERS})
            ) {
                /** @var ProductRepo $repo */
                $repo = Database::getRepo('XLite\Model\Product');

                $results = $repo->getCloudSearchResults($this->getCloudSearchConditions());

                if ($results !== null) {
                    foreach ($results['products'] as $p) {
                        if ($p['id'] == $product->getProductId() && !empty($p['variants'])) {
                            $params[ListItem::PARAM_CLOUD_FILTERS_FILTER_VARIANTS] = $p['variants'];

                            break;
                        }
                    }
                }
            }
        }

        return $params;
    }
}