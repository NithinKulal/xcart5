<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\ItemsList\Model\Order\Admin;

/**
 * Search total block (for order list page)
 *
 * @ListChild (list="pager.admin.model.table.right", weight="100", zone="admin")
 */
class SearchTotal extends \XLite\View\ItemsList\Model\Order\Admin\Search
{
    /*
     * The number of the cells from the end of table to the "Search total" cell
     */
    const SEARCH_TOTAL_CELL_NUMBER_FROM_END = 3;

    /**
     * Return list of targets allowed for this widget
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $result = parent::getAllowedTargets();
        $result[] = 'order_list';

        return $result;
    }

    /**
     * Re-define session cell name to get search condition from search orders list session cell
     *
     * @return string
     */
    public static function getSessionCellName()
    {
        return str_replace('\\', '', 'XLite\View\ItemsList\Model\Order\Admin\Search');
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'pager/model/table/parts/search_total.block.twig';
    }

    /**
     * Return number of the search total cell (for "colspan" option)
     *
     * @return array
     */
    protected function getSearchTotalCellColspan()
    {
        $cellNumber = parent::getColumnsCount();

        if ($cellNumber) {
            $cellNumber = $cellNumber - static::SEARCH_TOTAL_CELL_NUMBER_FROM_END + 1;
        }

        return $cellNumber;
    }

    /**
     * Search total amount
     *
     * @return \Doctrine\ORM\PersistentCollection
     */
    protected function getSearchTotals()
    {
        // Get search conditions
        $cnd = \XLite\View\ItemsList\Model\Order\Admin\Search::getSearchCaseProcessor()
                ->getSearchCase();

        foreach ($cnd as $modelParam => $value) {
            if (is_string($value)) {
                $value = trim($value);
                if (static::PARAM_DATE_RANGE === $modelParam && $value) {
                    $value = \XLite\View\FormField\Input\Text\DateRange::convertToArray($value);
                }
            }

            $cnd->{$modelParam} = $value;
        }

        return \XLite\Core\Database::getRepo('XLite\Model\Order')->search($cnd, \XLite\Model\Repo\Order::SEARCH_MODE_TOTALS);
    }

    /**
     * Get count of the search total amounts
     *
     * @return integer
     */
    protected function getSearchTotalsCount()
    {
        return count($this->getSearchTotals());
    }

    /**
     * Get count of the search total amounts
     *
     * @param integer $index Current search total index
     *
     * @return integer
     */
    protected function isNeedSearchTotalsSeparator($index)
    {
        $searchTotalsCount = $this->getSearchTotalsCount();

        return 1 < $searchTotalsCount
            && $index < $searchTotalsCount - 1;
    }

    /**
     * Get currency for the search total
     *
     * @param integer $currencyId Currency id
     *
     * @return \XLite\Model\Currency
     */
    protected function getSearchTotalCurrency($currencyId)
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Currency')
            ->findOneBy(array('currency_id' => $currencyId));
    }
}
