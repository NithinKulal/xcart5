<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\QSL\CloudSearch\View\ItemsList\Product\Customer;

/**
 * Search
 */
abstract class Search extends \XLite\View\ItemsList\Product\Customer\Search implements \XLite\Base\IDecorator
{
    const SORT_BY_RELEVANCE = 'relevance';
    /**
     * Define and set widget attributes; initialize widget
     *
     * @param array $params Widget params OPTIONAL
     *
     * @return void
     */
    public function __construct(array $params = array())
    {
        parent::__construct($params);

        if (\XLite\Module\QSL\CloudSearch\Main::doSearch()) {
            $this->sortByModes += array(
                static::SORT_BY_RELEVANCE => 'Relevance',
            );
        }
    }

    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        if (\XLite\Module\QSL\CloudSearch\Main::doSearch()) {
            $list[] = 'modules/QSL/CloudSearch/search_style.css';
        }

        return $list;
    }

    /**
     * Get products 'sort by' fields
     *
     * @return array
     */
    protected function getSortByFields()
    {
        $fields = parent::getSortByFields();

        if (\XLite\Module\QSL\CloudSearch\Main::doSearch()) {
            $fields += array(
                'relevance' => static::SORT_BY_RELEVANCE,
            );
        }

        return $fields;
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
        return static::SORT_BY_RELEVANCE === $this->getSortBy() ? '' : parent::getSortArrowClassCSS($sortBy);
    }

    /**
     * getSortOrder
     *
     * @return string
     */
    protected function getSortOrder()
    {
        return static::SORT_BY_RELEVANCE === $this->getSortBy() ? static::SORT_ORDER_DESC : parent::getSortOrder();
    }
}
