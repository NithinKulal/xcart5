<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\ItemsList\Model\Product\Admin;

use XLite\Core;
use XLite\Model;
use XLite\Model\WidgetParam;

/**
 * Top selling products list (for dashboard page)
 */
class TopSellers extends \XLite\View\ItemsList\Model\Product\Admin\LowInventoryBlock
{
    /**
     * Widget parameter name
     */
    const PARAM_PERIOD = 'period';
    const PARAM_AVAILABILITY  = 'availability';
    const PARAM_PRODUCTS_LIMIT = 'products_limit';

    /**
     * Allowed values for PARAM_PERIOD parameter
     */
    const P_PERIOD_DAY      = 'day';
    const P_PERIOD_WEEK     = 'week';
    const P_PERIOD_MONTH    = 'month';
    const P_PERIOD_LIFETIME = 'lifetime';

    /**
     * Get allowed periods
     *
     * @return array
     */
    public static function getAllowedPeriods()
    {
        return [
            self::P_PERIOD_DAY      => 'Last 24 hours',
            self::P_PERIOD_WEEK     => 'Last 7 days',
            self::P_PERIOD_MONTH    => 'Last month',
            self::P_PERIOD_LIFETIME => 'Store lifetime',
        ];
    }

    /**
     * Get allowed availability
     *
     * @return array
     */
    public static function getAllowedAvailability()
    {
        return [
            \XLite\Controller\Admin\TopSellers::AVAILABILITY_ALL => 'All',
            \XLite\Controller\Admin\TopSellers::AVAILABILITY_AVAILABLE_ONLY => 'Only available',
        ];
    }

    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        return [];
    }

    /**
     * Hide 'More...' link
     *
     * @return string
     */
    public function getMoreLink()
    {
        return null;
    }

    /**
     * Hide 'More...' link
     *
     * @return string
     */
    public function getMoreLinkTitle()
    {
        return null;
    }

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_PERIOD         => new WidgetParam\TypeString('Period', self::P_PERIOD_LIFETIME),
            static::PARAM_AVAILABILITY => new \XLite\Model\WidgetParam\TypeString(
                'Availability', \XLite\Controller\Admin\TopSellers::AVAILABILITY_ALL
            ),
            static::PARAM_PRODUCTS_LIMIT => new WidgetParam\TypeInt('Number of products', 5),
        ];
    }

    /**
     * Define items list columns
     *
     * @return array
     */
    protected function defineColumns()
    {
        $allowedColumns = [
            'sku',
            'name',
            'sold',
        ];

        $columns = parent::defineColumns();

        $columns['sold'] = [
            static::COLUMN_NAME  => Core\Translation::lbl('Sold'),
            static::COLUMN_ORDERBY  => 10000,
        ];

        // Remove redundant columns
        foreach ($columns as $k => $v) {
            if (!in_array($k, $allowedColumns, true)) {
                unset($columns[$k]);
            }
        }

        return $columns;
    }

    /**
     * getEmptyListTemplate
     *
     * @return string
     */
    protected function getEmptyListTemplate()
    {
        return $this->getDir() . '/' . $this->getPageBodyDir() . '/product/empty_top_sellers_list.twig';
    }

    /**
     * Get search conditions
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getSearchCondition()
    {
        $cnd = new Core\CommonCell();

        $cnd->date = [$this->getStartDate(), 0];
        $cnd->availability = $this->getAvailability();

        $cnd->limit = $this->getParam(self::PARAM_PRODUCTS_LIMIT);

        return $cnd;
    }

    /**
     * Get availability
     *
     * @return integer
     */
    protected function getAvailability()
    {
        return $this->getParam(self::PARAM_AVAILABILITY) ?: \XLite\Controller\Admin\TopSellers::AVAILABILITY_ALL;
    }

    /**
     * Get period start date timestamp
     *
     * @return integer
     */
    protected function getStartDate()
    {
        $now = Core\Converter::time();

        switch ($this->getParam(self::PARAM_PERIOD)) {
            case self::P_PERIOD_DAY:
                $startDate = mktime(0, 0, 0, date('m', $now), date('d', $now), date('Y', $now));
                break;

            case self::P_PERIOD_WEEK:
                $startDate = $now - (date('w', $now) * 86400);
                break;

            case self::P_PERIOD_MONTH:
                $startDate = mktime(0, 0, 0, date('m', $now), 1, date('Y', $now));
                break;

            case self::P_PERIOD_LIFETIME:
            default:
                $startDate = 0;
                break;
        }

        return $startDate;
    }

    /**
     * Get data for items list
     *
     * @param \XLite\Core\CommonCell $cnd       Search conditions
     * @param boolean                $countOnly Count only flag OPTIONAL
     *
     * @return array
     */
    protected function getData(Core\CommonCell $cnd, $countOnly = false)
    {
        list($start,) = $cnd->date;

        if (0 === (int) $start) {
            $data = Core\Database::getRepo('XLite\Model\Product')
                ->getTopSellers($this->getSearchCondition(), $countOnly);

        } else {
            $data = Core\Database::getRepo('XLite\Model\OrderItem')
                ->getTopSellers($this->getSearchCondition(), $countOnly);
        }

        return $countOnly
            // $data is a quantity of collection
            ? $data
            // $data is a collection and we must extract product data from it
            : array_map([$this, 'extractProductData'], $data);
    }

    /**
     * Extract product info from order item
     * It is used in the collection cycle for self::getData method
     *
     * @see \XLite\View\ItemsList\Model\Product\Admin\TopSellers::getData
     *
     * @param array $item Item
     *
     * @return \XLite\Model\Product
     */
    public function extractProductData($item)
    {
        $product = $item[0] instanceof Model\Product ? $item[0] : $item[0]->getProduct();
        $product->setSold($item['cnt']);

        return $product;
    }

    /**
     * We add the "removed" text for the products which were removed from the catalog
     *
     * @param string               $value  Product name
     * @param array                $column Column data
     * @param \XLite\Model\Product $entity Product model
     *
     * @return string
     */
    protected function preprocessName($value, array $column, Model\Product $entity)
    {
        return $entity->isPersistent() ? $value : ($value . ' <span class="removed">(removed)</span>');
    }

    /**
     * Check if the column must be a link.
     * It is used if the column field is displayed via
     *
     * @param array                $column Column
     * @param \XLite\Model\AEntity $entity Entity
     *
     * @return boolean
     */
    protected function isLink(array $column, Model\AEntity $entity)
    {
        return parent::isLink($column, $entity)
            // Deleted product entity must not be displayed as a link
            && $entity->isPersistent();
    }
}
