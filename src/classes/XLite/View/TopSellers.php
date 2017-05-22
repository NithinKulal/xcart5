<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

/**
 * Top sellers
 */
class TopSellers extends \XLite\View\RequestHandler\ARequestHandler
{
    const PARAM_TIME_INTERVAL = 'time_interval';
    const PARAM_AVAILABILITY  = 'availability';

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_TIME_INTERVAL => new \XLite\Model\WidgetParam\TypeString(
                'Time interval', \XLite\Controller\Admin\Stats::P_ALL
            ),
            static::PARAM_AVAILABILITY => new \XLite\Model\WidgetParam\TypeString(
                'Availability', \XLite\Controller\Admin\TopSellers::AVAILABILITY_ALL
            ),
        ];
    }

    /**
     * Define the "request" parameters
     *
     * @return void
     */
    protected function defineRequestParams()
    {
        parent::defineRequestParams();

        $this->requestParams[] = static::PARAM_TIME_INTERVAL;
        $this->requestParams[] = static::PARAM_AVAILABILITY;
    }

    /**
     * Return selected time interval
     *
     * @return string
     */
    public function getTimeInterval()
    {
        $timeInterval = $this->getParam(static::PARAM_TIME_INTERVAL);

        return $timeInterval;
    }

    /**
     * Return availability
     *
     * @return string
     */
    public function getAvailability()
    {
        $availability = $this->getParam(static::PARAM_AVAILABILITY);

        return $availability;
    }

    /**
     * Get dir
     *
     * @return string
     */
    protected function getDir()
    {
        return 'top_sellers';
    }

    /**
     * Return default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->getDir() . '/body.twig';
    }

    /**
     * Get CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = [
            'file'  => $this->getDir() . '/style.less',
            'media' => 'screen',
            'merge' => 'bootstrap/css/bootstrap.less',
        ];

        return $list;
    }

    /**
     * Get JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = $this->getDir() . '/controller.js';

        return $list;
    }

    /**
     * Build link for time interval
     *
     * @param string $interval time interval, see \XLite\Controller\Admin\Stats::getTimeIntervals
     *
     * @return string
     */
    public function getIntervalLink($interval)
    {
        return $this->buildURL('top_sellers', '', [
            static::PARAM_TIME_INTERVAL => $interval
        ]);
    }

    /**
     * Build link for availability
     *
     * @param string $availability
     *
     * @return string
     */
    public function getAvailabilityLink($availability)
    {
        return $this->buildURL('top_sellers', '', [
            static::PARAM_AVAILABILITY => $availability
        ]);
    }

    /**
     * Prepare statistics table
     *
     * @return array
     */
    public function getIntervalStats()
    {
        $stats = $this->getStats();

        $result = [];
        $timeInterval = $this->getTimeInterval();

        foreach ($stats as $stat) {
            $result[] = isset($stat[$timeInterval]) ? $stat[$timeInterval] : null;
        }

        return $result;
    }

    /**
     * Process position value
     *
     * @param int                           $id
     * @param \XLite\Model\OrderItem | null $item
     *
     * @return string
     */
    public function processPositionValue($id, $item)
    {
        return ($id + 1) . '.';
    }

    /**
     * Return item name
     *
     * @param \XLite\Model\OrderItem $item
     *
     * @return string
     */
    public function processName($item)
    {
        if ($item) {
            return $item->getObject()
                ? $item->getObject()->getName()
                : $item->getName() . ' ' . static::t('deleted');
        }

        return '&mdash;';
    }
}