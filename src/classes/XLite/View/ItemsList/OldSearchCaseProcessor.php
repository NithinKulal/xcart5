<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\ItemsList;

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
     * @var \XLite\View\ItemsList\ISearchValuesStorage
     */
    protected $searchValuesStorage;

    /**
     * @param array     $searchParams    Search params list
     * @param string    $sessionCellName Session cell name
     */
    public function __construct(array $searchParams, \XLite\View\ItemsList\ISearchValuesStorage $searchValuesStorage)
    {
        $this->searchParams         = $searchParams;
        $this->searchValuesStorage  = $searchValuesStorage;
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
}
