<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CanadaPost\View\ItemsList\Model;

/**
 * Products returns list
 */
class ProductsReturn extends \XLite\View\ItemsList\Model\Table
{
    /**
     * Allowed sort criterions
     */
    const SORT_BY_MODE_ID       = 'r.id';
    const SORT_BY_MODE_ORDER_ID = 'o.order_id';
    const SORT_BY_MODE_STATUS   = 'r.status';
    const SORT_BY_MODE_DATE     = 'r.date';
    //const SORT_BY_MODE_PROFILE  = '';

    /**
     * Widget param names
     */
    const PARAM_STATUS     = 'status';
    const PARAM_SUBSTRING  = 'substring';
    const PARAM_DATE_RANGE = 'dateRange';

    /**
     * Define and set widget attributes; initialize widget
     *
     * @param array $params Widget params (OPTIONAL)
     *
     * @return void
     */
    public function __construct(array $params = array())
    {
        $this->defineSortByParams();

        parent::__construct($params);
    }

    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/XC/CanadaPost/returns_search/list/style.css';

        return $list;
    }

    /**
     * Return search parameters
     *
     * @return array
     */
    static public function getSearchParams()
    {
        return array(
            \XLite\Module\XC\CanadaPost\Model\Repo\ProductsReturn::P_STATUS     => static::PARAM_STATUS,
            \XLite\Module\XC\CanadaPost\Model\Repo\ProductsReturn::P_DATE_RANGE => static::PARAM_DATE_RANGE,
            \XLite\Module\XC\CanadaPost\Model\Repo\ProductsReturn::P_SUBSTRING  => static::PARAM_SUBSTRING,
        );
    }

    /**
     * Define columns structure
     *
     * @return array
     */
    protected function defineColumns()
    {
        return array(
            'id'                        => array(
                static::COLUMN_ORDERBY  => 100,
                static::COLUMN_NAME     => static::t('Return #'),
                static::COLUMN_NO_WRAP  => true,
                static::COLUMN_LINK     => 'capost_return',
                static::COLUMN_SORT     => static::SORT_BY_MODE_ID,
            ),
            'order_number'              => array(
                static::COLUMN_ORDERBY  => 200,
                static::COLUMN_NAME     => static::t('Order #'),
                static::COLUMN_NO_WRAP  => true,
                static::COLUMN_TEMPLATE => 'modules/XC/CanadaPost/returns_search/list/cells/order_number.twig',
                static::COLUMN_SORT     => static::SORT_BY_MODE_ORDER_ID,
            ),
            'status'                    => array(
                static::COLUMN_ORDERBY  => 300,
                static::COLUMN_NAME     => static::t('Status'),
                static::COLUMN_NO_WRAP  => true,
                static::COLUMN_CLASS    => 'XLite\Module\XC\CanadaPost\View\FormField\Inline\ReturnStatus',
                static::COLUMN_SORT     => static::SORT_BY_MODE_STATUS,
            ),
            'date'                      => array(
                static::COLUMN_ORDERBY  => 400,
                static::COLUMN_NAME     => static::t('Date'),
                static::COLUMN_NO_WRAP  => true,
                static::COLUMN_SORT     => static::SORT_BY_MODE_DATE
            ),
            'profile'                   => array(
                static::COLUMN_ORDERBY  => 500,
                static::COLUMN_NAME     => static::t('Customer'),
                static::COLUMN_NO_WRAP  => true,
                static::COLUMN_MAIN     => true,
                static::COLUMN_TEMPLATE => 'modules/XC/CanadaPost/returns_search/list/cells/profile.twig',
                //static::COLUMN_SORT     => static::SORT_BY_MODE_PROFILE,
            ),
            'total'                     => array(
                static::COLUMN_ORDERBY  => 600,
                static::COLUMN_NAME     => static::t('Amount'),
                static::COLUMN_NO_WRAP  => true,
                static::COLUMN_TEMPLATE => 'modules/XC/CanadaPost/returns_search/list/cells/total.twig',
            ),
        );
    }

    /**
     * Define SORT_BY params
     *
     * @return void
     */
    protected function defineSortByParams()
    {
        $this->sortByModes += array(
            static::SORT_BY_MODE_ID       => 'Return #',
            static::SORT_BY_MODE_ORDER_ID => 'Order #',
            static::SORT_BY_MODE_STATUS   => 'Status',
            static::SORT_BY_MODE_DATE     => 'Date',
            //static::SORT_BY_MODE_PROFILE  => 'Customer',
        );
    }

    /**
     * Get default sort order
     *
     * @return string
     */
    protected function getSortOrderModeDefault()
    {
        return static::SORT_ORDER_DESC;
    }

    /**
     * getSortByModeDefault
     *
     * @return string
     */
    protected function getSortByModeDefault()
    {
        return static::SORT_BY_MODE_ID;
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
            static::PARAM_STATUS     => new \XLite\Model\WidgetParam\TypeSet(
                'Status', null, array_keys(\XLite\Module\XC\CanadaPost\Model\ProductsReturn::getAllowedStatuses())
            ),
            static::PARAM_DATE_RANGE => new \XLite\Model\WidgetParam\TypeString('Date range', ''),
            static::PARAM_SUBSTRING  => new \XLite\Model\WidgetParam\TypeString('Substring', ''),
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

        $this->requestParams = array_merge($this->requestParams, static::getSearchParams());
    }

    /**
     * Return params list to use for search
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getSearchCondition()
    {
        $result = parent::getSearchCondition();

        // We initialize structure to define order (field and sort direction) in search query.

        $result->{\XLite\Module\XC\CanadaPost\Model\Repo\ProductsReturn::P_ORDER_BY} = $this->getOrderBy();

        foreach (static::getSearchParams() as $modelParam => $requestParam) {

            $value = $this->getParam($requestParam);

            if (static::PARAM_DATE_RANGE === $requestParam && is_array($value)) {
                foreach ($value as $i => $date) {
                    if (is_string($date) && false !== strtotime($date)) {
                        $value[$i] = strtotime($date);
                    }
                }

            } elseif (is_string($value)) {
                $value = trim($value);
                if (static::PARAM_DATE_RANGE === $requestParam && $value) {
                    $value = \XLite\View\FormField\Input\Text\DateRange::convertToArray($value);
                }
            }

            $result->$modelParam = $value;

            if (
                isset($value)
                && '' !== $value
                && 0 !== $value
            ) {
                $result->$modelParam = $this->getParam($requestParam);
            }
        }

        return $result;
    }

    /**
     * Define repository name
     *
     * @return string
     */
    protected function defineRepositoryName()
    {
        return 'XLite\Module\XC\CanadaPost\Model\ProductsReturn';
    }

    /**
     * Get container class
     *
     * @return string
     */
    protected function getContainerClass()
    {
        return parent::getContainerClass() . ' capost-returns-list';
    }

    /**
     * Get pager parameters
     *
     * @return array
     */
    protected function getPagerParams()
    {
        $params = parent::getPagerParams();

        $params[\XLite\View\Pager\APager::PARAM_ITEMS_PER_PAGE] = 50;

        return $params;
    }

    /**
     * Get panel class
     *
     * @return \XLite\Module\XC\CanadaPost\View\StickyPanel\ItemsList\ProductsReturn
     */
    protected function getPanelClass()
    {
        return 'XLite\Module\XC\CanadaPost\View\StickyPanel\ItemsList\ProductsReturn';
    }

    /**
     * Check - order's profile removed or not
     *
     * @param \XLite\Model\Order $order Order
     *
     * @return boolean
     */
    protected function isProfileRemoved(\XLite\Model\Order $order)
    {
        return (!$order->getOrigProfile() || $order->getOrigProfile()->getOrder());
    }

    // {{{ Preprocessors

    /**
     * Preprocess "id" feild
     *
     * @param integer                                          $id     Canada Post return ID
     * @param array                                            $column Column data
     * @param \XLite\Module\XC\CanadaPost\Model\ProductsReturn $entity Canada Post return model
     *
     * @return string
     */
    protected function preprocessId($id, array $column, \XLite\Module\XC\CanadaPost\Model\ProductsReturn $entity)
    {
        return '#' . str_repeat('0', 5 - min(5, strlen($id))) . $id;
    }

    /**
     * Preprocess "id" feild
     *
     * @param integer                                          $id     Canada Post return ID
     * @param array                                            $column Column data
     * @param \XLite\Module\XC\CanadaPost\Model\ProductsReturn $entity Canada Post return model
     *
     * @return string
     */
    protected function preprocessOrderNumber($id, array $column, \XLite\Module\XC\CanadaPost\Model\ProductsReturn $entity)
    {
        return $entity->getOrder()->getPrintableOrderNumber();
    }

    /**
     * Preprocess "date" field
     *
     * @param integer                                          $date   Date
     * @param array                                            $column Column data
     * @param \XLite\Module\XC\CanadaPost\Model\ProductsReturn $entity Canada Post return model
     *
     * @return string
     */
    protected function preprocessDate($date, array $column, \XLite\Module\XC\CanadaPost\Model\ProductsReturn $entity)
    {
        return ($date)
            ? \XLite\Core\Converter::getInstance()->formatTime($date)
            : static::t('Unknown');
    }

    // }}}

    /**
     * Return title
     *
     * @return string
     */
    protected function getHead()
    {
        return 'Returns';
    }
}
