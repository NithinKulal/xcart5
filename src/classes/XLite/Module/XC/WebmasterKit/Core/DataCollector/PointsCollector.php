<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\WebmasterKit\Core\DataCollector;


class PointsCollector extends \XLite\Base\Singleton
{
    /**
     * List of points
     *
     * @var array
     */
    protected $points = [];

    protected $useXdebugStackTrace;

    /**
     * @inheritDoc
     */
    public function __construct()
    {
        parent::__construct();

        $this->useXdebugStackTrace = function_exists('xdebug_start_trace')
            && \XLite\Core\Config::getInstance()->XC->WebmasterKit->xdebugLogTrace;

    }


    /**
     * @return array
     */
    public function getCollected()
    {
        return $this->points;
    }

    /**
     * Add memory measure point
     *
     * @return void
     */
    public function addPoint($timePoint, $additional = false)
    {
        if (!isset($this->points[$timePoint])) {
            $this->points[$timePoint] = array(
                'start' => microtime(true),
                'open'  => true,
                'time'  => 0,
            );
            if ($this->useXdebugStackTrace) {
                @xdebug_start_trace(
                    LC_DIR_LOG . \Includes\Utils\FileManager::sanitizeFilename($timePoint) . '.' . microtime(true),
                    XDEBUG_TRACE_COMPUTERIZED
                );
            }

        } elseif ($this->points[$timePoint]['open']) {

            $range = microtime(true) - $this->points[$timePoint]['start'];
            if ($additional) {
                $this->points[$timePoint]['time'] += $range;
            } else {
                $this->points[$timePoint]['time'] = $range;
            }
            $this->points[$timePoint]['open'] = false;

            if ($this->useXdebugStackTrace) {
                @xdebug_stop_trace();
            }

        } else {

            $this->points[$timePoint]['start'] = microtime(true);
            $this->points[$timePoint]['open'] = true;

            if ($this->useXdebugStackTrace) {
                @xdebug_start_trace(
                    LC_DIR_VAR . 'log' . LC_DS . \Includes\Utils\FileManager::sanitizeFilename($timePoint) . '.' . microtime(true),
                    XDEBUG_TRACE_COMPUTERIZED
                );
            }

        }
    }

    /**
     * @param $name
     *
     * @return bool
     */
    public function hasPoint($name)
    {
        return isset($this->points[$name]);
    }

    /**
     * @param $name
     *
     * @return int
     */
    public function getPointData($name)
    {
        return isset($this->points[$name]['end'])
            ? ($this->points[$name]['end'] - $this->points[$name]['start'])
            : 0;
    }
}