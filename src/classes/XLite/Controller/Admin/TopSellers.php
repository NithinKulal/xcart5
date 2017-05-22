<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

/**
 * Top sellers statistics page controller
 */
class TopSellers extends \XLite\Controller\Admin\Stats
{
    const AVAILABILITY_ALL            = 'all';
    const AVAILABILITY_AVAILABLE_ONLY = 'available_only';

    /**
     * Number of positions
     */
    const TOP_SELLERS_NUMBER = 10;

    /**
     * Check ACL permissions
     *
     * @return boolean
     */
    public function checkACL()
    {
        return parent::checkACL() || \XLite\Core\Auth::getInstance()->isPermissionAllowed('manage orders');
    }

    /**
     * getPageTemplate
     *
     * @return string
     */
    public function getPageTemplate()
    {
        return 'top_sellers.twig';
    }

    /**
     * Get rows count in statistics
     *
     * @return integer
     */
    public function getRowsCount()
    {
        return self::TOP_SELLERS_NUMBER;
    }

    /**
     * @inheritdoc
     */
    protected function getTimeIntervals()
    {
        return array_unique(array_merge(
            [self::P_ALL],
            parent::getTimeIntervals()
        ));
    }

    /**
     * Get columns for statistics table
     *
     * @return array
     */
    public function getStatsRows()
    {
        return array_keys(array_fill(0, $this->getRowsCount(), ''));
    }

    /**
     * Prepare statistics table
     *
     * @return array
     */
    public function getStats()
    {
        parent::getStats();

        $this->stats = $this->processData($this->getData());

        return $this->stats;
    }

    /**
     * Return availability columns
     *
     * @return array
     */
    public function getAvailabilityColumns()
    {
        return [
            static::AVAILABILITY_ALL,
            static::AVAILABILITY_AVAILABLE_ONLY,
        ];
    }

    /**
     * Get column headings
     *
     * @return array
     */
    public function getAvailabilityColumnTitles()
    {
        return array(
            static::AVAILABILITY_ALL => 'All',
            static::AVAILABILITY_AVAILABLE_ONLY => 'Only available',
        );
    }

    /**
     * Get column heading
     *
     * @param string $column Column identificator
     *
     * @return array|string
     */
    public function getAvailabilityColumnTitle($column)
    {
        return \Includes\Utils\ArrayManager::getIndex($this->getAvailabilityColumnTitles(), $column);
    }

    /**
     * Get data
     *
     * @return array
     */
    protected function getData()
    {
        $data = [];

        foreach ($this->getStatsColumns() as $interval) {
            $condition = $this->defineDetDataCondition($interval);

            $data[$interval] = \XLite\Core\Database::getRepo('\XLite\Model\OrderItem')->getTopSellers($condition);
        }

        return $data;
    }

    /**
     * Return availability
     *
     * @return mixed
     */
    public function getAvailability()
    {
        $availability = \XLite\Core\Request::getInstance()->{\XLite\View\TopSellers::PARAM_AVAILABILITY};

        if (null === $availability) {
            $sessionCell = \XLite\Core\Session::getInstance()->{\XLite\View\TopSellers::getSessionCellName()};
            $availability = isset($sessionCell[\XLite\View\TopSellers::PARAM_AVAILABILITY])
                ? $sessionCell[\XLite\View\TopSellers::PARAM_AVAILABILITY]
                : static::AVAILABILITY_ALL;
        }

        return $availability;
    }

    /**
     * Define condition fo getData
     *
     * @param string $interval Time interval
     *
     * @return \XLite\Core\CommonCell
     */
    protected function defineDetDataCondition($interval)
    {
        $condition = $this->getSearchCondition($interval);
        $condition->limit = self::TOP_SELLERS_NUMBER;
        $condition->availability = $this->getAvailability();

        $currency = null;

        if (\XLite\Core\Request::getInstance()->currency) {
            $currency = \XLite\Core\Database::getRepo('XLite\Model\Currency')
                ->find(\XLite\Core\Request::getInstance()->currency);
        }

        if (!$currency) {
            $currency = \XLite::getInstance()->getCurrency();
        }

        $condition->currency = $currency->getCurrencyId();

        return $condition;
    }

    /**
     * processData
     *
     * @param array $data Collected data
     *
     * @return array
     */
    protected function processData($data)
    {
        $stats = $this->stats;

        foreach ($this->stats as $rownum => $periods) {

            foreach ($periods as $period => $val) {

                $stats[$rownum][$period] = (
                    is_array($data[$period])
                    && \Includes\Utils\ArrayManager::getIndex($data[$period], $rownum)
                )
                    ? $data[$period][$rownum][0]
                    : null;
            }
        }

        return $stats;
    }
}
