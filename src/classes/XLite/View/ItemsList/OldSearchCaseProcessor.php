<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\ItemsList;

use XLite\View\ItemsList\ISearchValuesStorage;

/**
 * SearchCaseProcessor
 */
class OldSearchCaseProcessor implements \XLite\View\ItemsList\ISearchCaseProvider
{
    /**
     * Search params
     *
     * @var array[\XLite\Model\SearchCondition\IExpressionProvider]
     */
    protected $searchParams;

    /**
     * Search values provider
     *
     * @var ISearchValuesStorage
     */
    protected $searchValuesStorage;

    /**
     * @param array                $searchParams        Search params list
     * @param ISearchValuesStorage $searchValuesStorage Search value storage
     */
    public function __construct(array $searchParams, ISearchValuesStorage $searchValuesStorage)
    {
        $this->searchParams        = $searchParams;
        $this->searchValuesStorage = $searchValuesStorage;
    }

    /**
     * Get search case
     *
     * @return \XLite\Core\CommonCell
     */
    public function getSearchCase()
    {
        $cell = new \XLite\Core\CommonCell();

        foreach ($this->searchParams as $name => $condition) {
            $cell->{$name} = $this->searchValuesStorage->getValue($condition);
        }

        return $cell;
    }

    /**
     * @return array
     */
    public function getSearchParams()
    {
        return $this->searchParams;
    }

    /**
     * @param array $searchParams
     */
    public function setSearchParams($searchParams)
    {
        $this->searchParams = $searchParams;
    }

    /**
     * @return ISearchValuesStorage
     */
    public function getSearchValuesStorage()
    {
        return $this->searchValuesStorage;
    }

    /**
     * @param ISearchValuesStorage $searchValuesStorage
     */
    public function setSearchValuesStorage($searchValuesStorage)
    {
        $this->searchValuesStorage = $searchValuesStorage;
    }
}
