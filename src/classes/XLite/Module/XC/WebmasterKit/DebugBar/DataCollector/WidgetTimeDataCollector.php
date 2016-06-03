<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\WebmasterKit\DebugBar\DataCollector;

use DebugBar\DataCollector\DataCollector;
use DebugBar\DataCollector\Renderable;
use DebugBar\DebugBarException;
use XLite\Module\XC\WebmasterKit\DebugBar\Doctrine\DBAL\Logging\ObservableDebugStack;
use XLite\Module\XC\WebmasterKit\Logic\DebugBarSettingsManager;

/**
 * Standard TimeDataCollector arranges time measures in the order of their stopMeasure calls. This data collector arranges them in the same order the startMeasure was called.
 */
class WidgetTimeDataCollector extends DataCollector implements Renderable
{
    protected $measuresStack = [];

    protected $measures = [];

    protected $widgetCount = 0;

    /**
     * @var float
     */
    protected $requestEndTime;

    /**
     * @var ObservableDebugStack
     */
    private $observableDebugStack;

    public function __construct(ObservableDebugStack $observableDebugStack)
    {
        $this->requestStartTime = isset($_SERVER['REQUEST_TIME_FLOAT'])
            ? $_SERVER['REQUEST_TIME_FLOAT']
            : microtime(true);

        $this->observableDebugStack = $observableDebugStack;

        $this->observableDebugStack->addStartQueryObserver([$this, 'startQuery']);
    }

    public function startQuery($sql, $params, $types)
    {
        $settingsMgr = new DebugBarSettingsManager();

        $query = ['sql' => $sql];

        if ($settingsMgr->areWidgetsSqlQueryStacktracesEnabled()) {
            $query['backtrace'] = array_slice(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS), 3);
        }

        foreach ($this->measuresStack as &$measure) {
            $measure['queries'][] = &$query;
        }
    }

    public function getName()
    {
        return 'widget_times';
    }

    public function getWidgets()
    {
        return [
            "widgets"       => [
                "icon"    => "tasks",
                "widget"  => "PhpDebugBar.XCartWidgets.TreeTimelineWidget",
                "map"     => "widget_times",
                "default" => "{}",
            ],
            "widgets:badge" => [
                "map"     => "widget_times.widget_count",
                "default" => 0,
            ],
            "time"          => [
                "icon"    => "clock-o",
                "tooltip" => "Request Duration",
                "map"     => "widget_times.duration_str",
                "default" => "'0ms'",
            ],
        ];
    }

    /**
     * Starts a measure
     *
     * @param string      $label     Public name
     * @param string|null $collector The source of the collector
     */
    public function startMeasure($label, $collector = null)
    {
        $start = microtime(true);

        $measure = [
            'label'          => $label,
            'start'          => $start,
            'relative_start' => $start - $this->requestStartTime,
            'collector'      => $collector,
            'children'       => [],
            'queries'        => [],
        ];

        if (empty($this->measuresStack)) {
            $this->measures[] = &$measure;
        } else {
            end($this->measuresStack);
            $current = &$this->measuresStack[key($this->measuresStack)];
            reset($this->measuresStack);

            $current['children'][] = &$measure;
        }

        $this->measuresStack[] = &$measure;
    }

    /**
     * Stops a measure
     *
     * @param array $params
     * @param array $widgetData Widget-associated data (visibility/cache status etc.)
     *
     * @throws DebugBarException
     */
    public function stopMeasure($params = [], $widgetData = [])
    {
        if (empty($this->measuresStack)) {
            throw new DebugBarException('DebugBar: Failed stopping measure because it hasn\'t been started');
        }

        $end = microtime(true);

        end($this->measuresStack);
        $current = &$this->measuresStack[key($this->measuresStack)];
        reset($this->measuresStack);

        $current += [
            'end'          => $end,
            'relative_end' => $end - $this->requestEndTime,
            'duration'     => $end - $current['start'],
            'params'       => $params,
            'cached'       => $widgetData['cached'],
            'visible'      => $widgetData['visible'],
        ];

        array_pop($this->measuresStack);

        $this->widgetCount++;
    }

    /**
     * Returns the duration of a request
     *
     * @return float
     */
    public function getRequestDuration()
    {
        if ($this->requestEndTime !== null) {
            return $this->requestEndTime - $this->requestStartTime;
        }

        return microtime(true) - $this->requestStartTime;
    }

    public function collect()
    {
        $this->requestEndTime = microtime(true);

        if (!empty($this->measuresStack)) {
            throw new DebugBarException('DebugBar: You have non-stopped measures.');
        }

        $calcDurations = function ($measure) use (&$calcDurations) {
            $durationStr = $this->getDataFormatter()->formatDuration($measure['duration']);

            $durationPercentage = round($measure['duration'] / $this->getRequestDuration() * 100, 2);

            $durationStr .= ', ' . $durationPercentage . '%';

            $measure['duration_str'] = $durationStr;

            $measure['children'] = array_map($calcDurations, $measure['children']);

            return $measure;
        };

        $measures = array_map($calcDurations, $this->measures);

        return [
            'start'        => $this->requestStartTime,
            'end'          => $this->requestEndTime,
            'duration'     => $this->getRequestDuration(),
            'duration_str' => $this->getDataFormatter()->formatDuration($this->getRequestDuration()),
            'measures'     => $measures,
            'widget_count' => $this->widgetCount,
        ];
    }
}