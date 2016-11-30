<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\ItemsList\Product\Customer;

/**
 * Enables caching for a widget
 */
trait DefaultSortByTrait
{
    /**
     * Get default sort order value
     *
     * @return string
     */
    protected function getDefaultSortOrderValue()
    {
        return \XLite\Core\Config::getInstance()->General->default_products_sort_order;
    }

    abstract protected function getSortByModesField();

    abstract protected function setSortByModesField($value);

    /**
     * Get additional sortByModes
     *
     * @return void
     */
    public function processAdditionalSortByModes()
    {
        if ('default' === $this->getDefaultSortOrderValue()) {
            $this->setSortByModesField(
                [
                    static::SORT_BY_MODE_DEFAULT => 'Default-sort-option',
                ] + $this->getSortByModesField()
            );
        }
    }

    /**
     * Get products 'sort by' fields
     *
     * @return array
     */
    protected function getSortByFields()
    {
        return [
            'default' => static::SORT_BY_MODE_DEFAULT,
        ] + parent::getSortByFields();
    }

    /**
     * Defines the CSS class for sorting order arrow
     *
     * @param string $sortBy
     *
     * @return string
     */
    protected function getSortArrowClassCSS($sortBy)
    {
        return static::SORT_BY_MODE_DEFAULT === $this->getSortBy() ? '' : parent::getSortArrowClassCSS($sortBy);
    }

    /**
     * getSortOrder
     *
     * @return string
     */
    protected function getSortOrder()
    {
        return static::SORT_BY_MODE_DEFAULT === $this->getSortBy() ? static::SORT_ORDER_ASC : parent::getSortOrder();
    }

    /**
     * getSortByModeDefault
     *
     * @return string
     */
    protected function getSortByModeDefault()
    {
        list($sortField, $sortMode) = $this->getDefaultSortOrderFromOption();

        return $sortField ?: parent::getSortByModeDefault();
    }

    /**
     * getSortOrderDefault
     *
     * @return string
     */
    protected function getSortOrderModeDefault()
    {
        list($sortField, $sortMode) = $this->getDefaultSortOrderFromOption();

        return $sortMode ?: parent::getSortOrderModeDefault();
    }

    /**
     * Get default sort order values from option
     * Returned an array(<sortField>, <asc|desc|null>)
     *
     * @return array
     */
    protected function getDefaultSortOrderFromOption()
    {
        // Parse option value
        preg_match(
            '/^(\w+)(Asc|Desc)?$/SU',
            $this->getDefaultSortOrderValue(),
            $match
        );

        // Get list of available sort fields
        $sortFields = $this->getSortByFields();

        $option = (!empty($match[1]) && !empty($sortFields[$match[1]]))
            ? $sortFields[$match[1]]
            : null;

        $sortMode = $option && !empty($match[2])
            ? strtolower($match[2])
            : null;

        return [$option, $sortMode];
    }
}
