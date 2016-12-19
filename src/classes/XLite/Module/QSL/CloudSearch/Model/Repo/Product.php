<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\QSL\CloudSearch\Model\Repo;

use Doctrine\ORM\QueryBuilder;
use XLite\Core\Auth;
use XLite\Core\CommonCell;
use XLite\Module\QSL\CloudSearch\Core\ServiceApiClient;
use XLite\Module\QSL\CloudSearch\Main;
use XLite\View\ItemsList\Product\Customer\Search as SearchList;

/**
 * The "product" repo class
 */
abstract class Product extends \XLite\Model\Repo\Product implements \XLite\Base\IDecorator
{
    const P_CLOUD_FILTERS = 'cloudFilters';

    const P_LOAD_PRODUCTS_WITH_CLOUD_SEARCH = 'loadProductsWithCloudSearch';

    protected $cloudSearchResults = [];

    protected $skipMembershipCondition = false;

    /**
     * Get CloudSearch search results under current searchState conditions
     *
     * @param CommonCell $cnd
     *
     * @return array|null
     */
    public function getCloudSearchResults(CommonCell $cnd)
    {
        $query           = $cnd->{self::P_SUBSTRING};
        $categoryId      = $cnd->{self::P_CATEGORY_ID};
        $searchInSubcats = $cnd->{self::P_SEARCH_IN_SUBCATS};
        $filters         = $cnd->{self::P_CLOUD_FILTERS};
        list($sortMode, $sortDir) = $this->getSortOrderValue($cnd->{self::P_ORDER_BY});
        list($offset, $limit) = $cnd->{self::P_LIMIT};

        // Workaround for a bug in X-Cart when it sets offset to negative values sometimes
        $offset = max($offset, 0);

        $membership = Auth::getInstance()->getMembershipId();

        $sortFieldMappings = $this->getCloudSearchSortFieldMappings();

        $sort = isset($sortFieldMappings[$sortMode])
            ? $sortFieldMappings[$sortMode] . ' ' . $sortDir : null;

        // Switch to relevance sorting on default sort mode if P_SUBSTRING is not empty
        if (
            $sortMode == SearchList::SORT_BY_MODE_DEFAULT
            && $query !== null
            && $query !== ''
        ) {
            $sort = null;
        }

        $searchParams = [
            $query,
            $categoryId,
            $searchInSubcats,
            $filters,
            $membership,
            $sort,
            $offset,
            $limit,
        ];

        $paramsHash = md5(serialize($searchParams));

        if (!array_key_exists($paramsHash, $this->cloudSearchResults)) {
            $apiClient = new ServiceApiClient();

            $this->cloudSearchResults[$paramsHash] = call_user_func_array(
                [$apiClient, 'search'],
                $searchParams
            );
        }

        return $this->cloudSearchResults[$paramsHash];
    }

    /**
     * Get product ids returned from CloudSearch
     *
     * @param CommonCell $cnd
     *
     * @return array|null
     */
    protected function getCloudSearchProductIds(CommonCell $cnd)
    {
        $results = $this->getCloudSearchResults($cnd);

        return array_map(function ($p) {
            return $p['id'];
        }, $results['products']);
    }

    /**
     * Get "X-Cart search mode -> CloudSearch sort field" mappings
     *
     * @return array
     */
    protected function getCloudSearchSortFieldMappings()
    {
        return [
            'cp.orderby'        => 'sort_int_orderby',
            'p.arrivalDate'     => 'sort_int_arrival_date',
            'p.price'           => 'sort_float_price',
            'translations.name' => 'sort_str_name',
            'r.rating'          => 'sort_float_rating',
            'p.sales'           => 'sort_int_sales',
        ];
    }

    /**
     * Prepare certain search condition
     *
     * @param QueryBuilder $queryBuilder Query builder to prepare
     * @param string       $value        Condition data
     *
     * @return void
     */
    protected function prepareCndSubstring(QueryBuilder $queryBuilder, $value)
    {
        if ($this->loadProductsWithCloudSearch()) {
            $ids = $this->getCloudSearchProductIds($this->getCloudSearchConditions());

            if (!empty($ids)) {
                $idsInCondition = $queryBuilder->getInCondition($ids, 'arr');
                $queryBuilder->andWhere('p.product_id IN (' . $idsInCondition . ')');
            } else {
                // Force empty result set:
                $queryBuilder->andWhere('p.product_id IN (0)');
            }
        } else {
            parent::prepareCndSubstring($queryBuilder, $value);
        }
    }

    /**
     * Prepare certain search condition
     *
     * @param QueryBuilder $queryBuilder Query builder to prepare
     * @param array        $value        Condition data
     */
    protected function prepareCndOrderBy(QueryBuilder $queryBuilder, array $value)
    {
        if ($this->loadProductsWithCloudSearch() && !$this->isCountSearchMode()) {
            $ids = $this->getCloudSearchProductIds($this->getCloudSearchConditions());

            if (!empty($ids)) {
                $idsInCondition = $queryBuilder->getInCondition($ids, 'arr');

                $queryBuilder->resetDQLPart('orderBy');
                $queryBuilder
                    ->addSelect('field(p.product_id, ' . $idsInCondition . ') as field_product_id')
                    ->addOrderBy('field_product_id', 'asc');
            }
        } else {
            parent::prepareCndOrderBy($queryBuilder, $value);
        }
    }

    /**
     * Prepare certain search condition
     *
     * @param QueryBuilder $queryBuilder Query builder to prepare
     * @param mixed        $value        Condition data
     *
     * @return void
     */
    protected function prepareCndCategoryId(QueryBuilder $queryBuilder, $value)
    {
        if ($this->loadProductsWithCloudSearch()) {
            $ids = $this->getCloudSearchProductIds($this->getCloudSearchConditions());

            if (!empty($ids)) {
                $idsInCondition = $queryBuilder->getInCondition($ids, 'arr');
                $queryBuilder->andWhere('p.product_id IN (' . $idsInCondition . ')');
            } else {
                // Force empty result set:
                $queryBuilder->andWhere('p.product_id IN (0)');
            }
        }

        parent::prepareCndCategoryId($queryBuilder, $value);
    }

    /**
     * Prepare certain search condition
     *
     * @param QueryBuilder $queryBuilder Query builder to prepare
     * @param array        $value        Condition data
     *
     * @return void
     */
    protected function prepareCndLimit(QueryBuilder $queryBuilder, array $value)
    {
        if (!$this->loadProductsWithCloudSearch()) {
            parent::prepareCndLimit($queryBuilder, $value);
        }
    }

    /**
     * Prepare certain search condition
     *
     * @param QueryBuilder $queryBuilder Query builder to prepare
     * @param string       $value        Condition data
     *
     * @return void
     */
    protected function prepareCndCloudFilters(QueryBuilder $queryBuilder, $value)
    {
        // No-op handler for the 'cloudFilters' search condition
    }

    /**
     * Prepare certain search condition
     *
     * @param QueryBuilder $queryBuilder Query builder to prepare
     * @param string       $value        Condition data
     *
     * @return void
     */
    protected function prepareCndLoadProductsWithCloudSearch(QueryBuilder $queryBuilder, $value)
    {
        // No-op handler for the 'loadProductsWithCloudSearch' search condition
    }

    /**
     * @return boolean
     */
    public function isSkipMembershipCondition()
    {
        return $this->skipMembershipCondition;
    }

    /**
     * @param boolean $skipMembershipCondition
     */
    public function setSkipMembershipCondition($skipMembershipCondition)
    {
        $this->skipMembershipCondition = $skipMembershipCondition;
    }

    /**
     * Adds additional condition to the query for checking if product has available membership
     *
     * @param QueryBuilder $queryBuilder Query builder object
     * @param string       $alias        Entity alias OPTIONAL
     *
     * @return void
     */
    protected function addMembershipCondition(QueryBuilder $queryBuilder, $alias = null)
    {
        if (!$this->isSkipMembershipCondition()) {
            parent::addMembershipCondition($queryBuilder, $alias);
        }
    }

    /**
     * Get current search conditions
     *
     * @return CommonCell
     */
    protected function getCloudSearchConditions()
    {
        return $this->searchState['currentSearchCnd'];
    }

    /**
     * Check if products should be fetched from CloudSearch
     *
     * @return mixed
     */
    protected function loadProductsWithCloudSearch()
    {
        /** @var CommonCell $cnd */
        $cnd = $this->getCloudSearchConditions();

        return Main::isConfigured()
               && $cnd->{self::P_LOAD_PRODUCTS_WITH_CLOUD_SEARCH}
               && $this->getCloudSearchResults($this->getCloudSearchConditions()) !== null;
    }
}