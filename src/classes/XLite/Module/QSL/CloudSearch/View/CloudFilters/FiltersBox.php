<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\QSL\CloudSearch\View\CloudFilters;


use XLite\Core\Auth;
use XLite\Core\Database;
use XLite\Model\WidgetParam\TypeBool;
use XLite\Model\WidgetParam\TypeCollection;
use XLite\Module\QSL\CloudSearch\Main;
use XLite\Module\QSL\CloudSearch\Model\Repo\Product as ProductRepo;
use XLite\Module\QSL\CloudSearch\Core\ServiceApiClient;
use XLite\Module\QSL\CloudSearch\Model\Repo\Product;

/**
 * Cloud filters sidebar box widget
 */
class FiltersBox extends \XLite\View\SideBarBox
{
    const PARAM_FILTER_CONDITIONS = 'filterConditions';
    const PARAM_IS_ASYNC_FILTERS  = 'isAsyncFilters';

    /**
     * Get a list of JS files required to display the widget
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = 'modules/QSL/CloudSearch/cloud_filters/filters.js';

        return $list;
    }

    /**
     * @return array
     */
    public function getCSSFiles()
    {
        $list   = parent::getCSSFiles();
        $list[] = [
            'file'  => 'modules/QSL/CloudSearch/cloud_filters/filters.less',
            'media' => 'screen',
        ];

        return $list;
    }

    /**
     * Register the CSS classes for this block
     *
     * @return string
     */
    protected function getBlockClasses()
    {
        return parent::getBlockClasses() . ' block-cloud-filters';
    }

    /**
     * Return templates directory name
     *
     * @return string
     */
    protected function getDir()
    {
        return 'modules/QSL/CloudSearch/cloud_filters/sidebar_box';
    }

    /**
     * Return default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/QSL/CloudSearch/cloud_filters/sidebar_box/container.twig';
    }

    /**
     * Return title
     *
     * @return string
     */
    protected function getHead()
    {
        return 'Filters';
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        if (!parent::isVisible() || !Main::isConfigured()) {
            return false;
        }

        if ($this->isAsyncFilters()) {
            return true;
        }

        $searchResults = $this->getSearchResults();

        return $searchResults !== null
               && $searchResults['facets'] !== null; // CloudFilters is disabled if service returned null in the 'facets' key
    }

    /**
     * Get current CloudSearch search results object
     *
     * @return array
     */
    protected function getSearchResults()
    {
        $searchResults = null;

        /** @var Product $repo */
        $repo = Database::getRepo('XLite\Model\Product');

        $searchResults = $repo->getCloudSearchResults($this->getCloudSearchConditions());

        return $searchResults;
    }

    /**
     * Get search query condition
     *
     * @return array
     */
    protected function getSearchQuery()
    {
        $conditions = $this->getCloudSearchConditions();

        return $conditions->{ProductRepo::P_SUBSTRING};
    }

    /**
     * Get category id search condition
     *
     * @return array
     */
    protected function getCategoryId()
    {
        $conditions = $this->getCloudSearchConditions();

        return $conditions->{ProductRepo::P_CATEGORY_ID};
    }

    /**
     * Get category id search condition
     *
     * @return array
     */
    protected function isSearchInSubcats()
    {
        $conditions = $this->getCloudSearchConditions();

        return $conditions->{ProductRepo::P_SEARCH_IN_SUBCATS};
    }

    /**
     * Get current filtering conditions
     *
     * @return array|\stdClass
     */
    protected function getFilterConditions()
    {
        $conditions = $this->getCloudSearchConditions();

        $filters = $conditions->{ProductRepo::P_CLOUD_FILTERS};

        return !empty($filters) ? $filters : new \stdClass();
    }

    /**
     * Register files from common repository
     *
     * @return array
     */
    public function getCommonFiles()
    {
        return array(
            static::RESOURCE_JS => array(
                array(
                    'file'      => $this->isDeveloperMode() ? 'vue/vue.js' : 'vue/vue.min.js',
                    'no_minify' => true,
                ),
            ),
        );
    }

    /**
     * Get commented widget data
     *
     * @return array
     */
    protected function getPhpToJsData()
    {
        $client = new ServiceApiClient();

        $searchApiUrl    = $client->getSearchApiUrl();
        $apiKey          = $client->getApiKey();
        $searchQuery     = $this->getSearchQuery();
        $categoryId      = $this->getCategoryId();
        $searchInSubcats = $this->isSearchInSubcats();

        $membership = Auth::getInstance()->getMembershipId();

        $currency = \XLite::getInstance()->getCurrency();

        $currencyFormat = [
            'prefix'             => $currency->getPrefix(),
            'suffix'             => $currency->getSuffix(),
            'decimalDelimiter'   => $currency->getDecimalDelimiter(),
            'thousandsDelimiter' => $currency->getThousandDelimiter(),
            'numDecimals'        => $currency->getE(),
        ];

        $data = [
            'filters'        => $this->getFilterConditions(),
            'facetApi'       => [
                'url'  => $searchApiUrl,
                'data' => [
                    'apiKey'          => $apiKey,
                    'q'               => $searchQuery,
                    'categoryId'      => $categoryId,
                    'searchInSubcats' => $searchInSubcats,
                    'facet'           => true,
                    'limits'          => [
                        'products'      => 0,
                        'categories'    => 0,
                        'manufacturers' => 0,
                        'pages'         => 0,
                    ],
                    'membership'      => $membership,
                ],
            ],
            'currencyFormat' => $currencyFormat,
        ];

        if (!$this->isAsyncFilters()) {
            $results = $this->getSearchResults();

            $data += [
                'facets'   => $results['facets'],
                'stats'    => $results['stats'],
                'numFound' => $results['numFoundProducts'],
            ];
        }

        return $data;
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
            self::PARAM_FILTER_CONDITIONS => new TypeCollection('Filter conditions', []),
            self::PARAM_IS_ASYNC_FILTERS  => new TypeBool('Filter conditions'),
        );
    }

    /**
     * @return \XLite\Core\CommonCell
     */
    protected function getCloudSearchConditions()
    {
        return $this->getParam(self::PARAM_FILTER_CONDITIONS);
    }

    /**
     * @return bool
     */
    protected function isAsyncFilters()
    {
        return $this->getParam(self::PARAM_IS_ASYNC_FILTERS);
    }
}